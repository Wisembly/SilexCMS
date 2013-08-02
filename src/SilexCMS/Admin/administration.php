<?php

use Silex\Application;

use SilexCMS\Form\Form;
use SilexCMS\Form\TableType;
use SilexCMS\Response\TransientResponse;

use Symfony\Component\HttpFoundation\Request;
use SilexCMS\Repository\GenericRepository;

$app->match('/administration/{table}', function (Application $app, $table) {

    if (is_null($app['silexcms.security']->getUsername())) {
        return $app->redirect($app['url_generator']->generate('index'));
    }

    $repository = new GenericRepository($app['db'], $table);
    $schema = $repository->getSchema();
    $rows = $repository->findAll(true);

    foreach ($rows as $row) {
        $data[] = array_map(function($val) {
            return is_string($val) && strlen($val) > 50 ? substr(strip_tags($val), 0, 47) . '...' : $val;
        }, $row);
    }

    return new TransientResponse($app, 'administration/administration_table.html.twig', array('table' => $table, 'fields' => $schema, 'rows' => $data));
})
->bind('administration_table');

$app->match('/administration/{table}/{id}', function (Application $app, Request $req, $table, $id) {

    if (is_null($app['silexcms.security']->getUsername())) {
        return $app->redirect($app['url_generator']->generate('index'));
    }

    if (!is_numeric($id) && 'new' !== $id) {
        throw new \Exception("Wrong parameters");
    }

    $repository = new GenericRepository($app['db'], $table);
    $formGenerator = new Form($repository);
    $form = $app['form.factory']->create(new TableType($app, $table), $formGenerator->getData('new' === $id ? null : $id));

    if ($req->getMethod() === 'POST') {
        $form->bindRequest($req);

        if ($form->isValid()) {
            $data = $form->getData();

            foreach ($data['row'] as $row) {
                $where = array('id' => $row['id']);
                unset($row['id']);

                if ('new' === $id) {
                    $repository->insert($row);

                    return $app->redirect($app['url_generator']->generate('administration_table', array('table' => $table)));
                } else {
                    $repository->update($row, $where);
                }

                // cache strategy. Update cache version
                $app['silexcms.cache.manager']->update();
            }
        }
    }

    return new TransientResponse($app, 'administration/administration_edit.html.twig', array(
        'table' => $table,
        'id'    => $id,
        'form'  => $form->createView()
    ));
})
->bind('administration_edit');

$app->match('/administration', function(Application $app) {

    if (is_null($app['silexcms.security']->getUsername())) {
        return $app->redirect($app['url_generator']->generate('index'));
    }

    $tables = $app['db']->fetchAll('SHOW tables');
    $listTables = array();

    foreach ($tables as $table) {
        $listTables[] = array_shift($table);
    }

    return new TransientResponse($app, 'administration/administration_hub.html.twig', array('tables' => $listTables));
})
->bind('administration_hub');