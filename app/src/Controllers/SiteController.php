<?php

namespace Zaibatsu\Controllers;

use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Zaibatsu\App;
use Zaibatsu\Controller;
use Doctrine\DBAL\Schema\Table;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * Class SiteController
 * @package Zaibatsu\Controllers
 */
class SiteController extends Controller
{
    /**
     * Главная страница
     *
     * @param App $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(App $app)
    {
        return $app->render('pages/index.twig');
    }

    /**
     * Отправка почты
     *
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function sendAction(App $app, Request $request)
    {
        $form = array(
            'message' => (string)$request->request->get('message'),
        );

        $constraint = new Assert\Collection(array(
            'message' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3, 'max' => 1000))),
        ));

        $errors = $app['validator']->validateValue($form, $constraint);

        $messages = array();

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }

            return JsonResponse::create(array('code' => 1, 'text' => implode(',', $messages)));
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Сообщение с сайта')
            ->setFrom(array('noreply@example.com'))
            ->setTo(array('mailbox@example.com'))
            ->setBody($app->renderView('blocks/email.twig', array('message' => $form['message'])))
            ->setContentType('text/html');

        /** @noinspection PhpParamsInspection */
        $app->mail($message);

        return JsonResponse::create(array('code' => 1, 'text' => 'Письмо успешно отправлено!'));
    }

    /**
     * Форма входа
     *
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(App $app, Request $request)
    {
        return $app->render('pages/login.twig', array(
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

    /**
     * Создание таблицы пользователей
     *
     * @param App $app
     * @return string
     */
    public function createAction(App $app)
    {
        /** @var $schema MySqlSchemaManager */
        $schema = $app['db']->getSchemaManager();

        if (!$schema->tablesExist('users')) {
            $users = new Table('users');
            $users->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
            $users->setPrimaryKey(array('id'));
            $users->addColumn('username', 'string', array('length' => 32));
            $users->addUniqueIndex(array('username'));
            $users->addColumn('password', 'string', array('length' => 255));
            $users->addColumn('roles', 'string', array('length' => 255));

            $schema->createTable($users);

            $app['db']->insert('users', array(
                'username' => 'user',
                'password' => (new MessageDigestPasswordEncoder())->encodePassword('user', ''),
                'roles' => 'ROLE_USER'
            ));

            $app['db']->insert('users', array(
                'username' => 'admin',
                'password' => (new MessageDigestPasswordEncoder())->encodePassword('admin', ''),
                'roles' => 'ROLE_ADMIN'
            ));
        }

        return 'Done!';
    }
}