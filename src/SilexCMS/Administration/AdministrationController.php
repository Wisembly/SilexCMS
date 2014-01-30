<?php

namespace SilexCMS\Administration;

use Silex\Application;

use SilexCMS\Form\Form;
use SilexCMS\Form\TableType;
use SilexCMS\Response\TransientResponse;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection as Database;

use Silex\ServiceProviderInterface;

class AdministrationController implements ServiceProviderInterface
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function boot(Application $app) {}

    public function register(Application $app)
    {
        $app->match('/administration/{table}', function ($table) use ($app) {

            if (is_null($app['silexcms.security']->getUsername())) {
                return $app->redirect($app['url_generator']->generate('administration_login'));
            }

            $repository = $app['silexcms.sets'][$table]->getRepository();
            $schema = $repository->getSchema();
            $rows = $repository->findAll(true);

            foreach ($rows as $row) {
                $data[] = array_map(function($val) {
                    return is_string($val) && strlen($val) > 50 ? substr(strip_tags($val), 0, 47) . '...' : $val;
                }, $row);
            }

            return new TransientResponse($app, $app['silexcms.template.loader']->load('administration/administration_table.html.twig'), array(
                'table'  => $table,
                'fields' => $schema,
                'rows'   => $data,
            ));
        })
        ->bind('administration_table');

        $app->match('/administration/{table}/{primaryKey}', function (Request $req, $table, $primaryKey) use ($app) {

            if (is_null($app['silexcms.security']->getUsername())) {
                return $app->redirect($app['url_generator']->generate('administration_login'));
            }

            $set = $app['silexcms.sets'][$table];
            $repository = $set->getRepository();
            $formGenerator = new Form($set);
            $form = $app['form.factory']->create(new TableType($app, $table), $formGenerator->getData('_new' === $primaryKey ? null : $primaryKey));

            if ($req->getMethod() === 'POST') {
                $form->bindRequest($req);

                if ($form->isValid()) {
                    $data = $form->getData();

                    foreach ($data['row'] as $row) {
                        // unset id primaryKey
                        if ('id' === $repository->getPrimaryKey()) {
                            unset($row[$repository->getPrimaryKey()]);
                        }

                        if ('_new' === $primaryKey) {
                            $repository->insert($row);

                            return $app->redirect($app['url_generator']->generate('administration_table', array('table' => $table)));
                        }

                        $repository->update($row, array('`'. $repository->getPrimaryKey() . '`' => $row[$repository->getPrimaryKey()]));

                        try {
                            // cache strategy if exist. Update cache version
                            $app['silexcms.cache.manager']->update();
                        } catch (\Exception $e) {}
                    }
                }
            }

            return new TransientResponse($app, $app['silexcms.template.loader']->load('administration/administration_edit.html.twig'), array(
                'table'         => $table,
                'primaryKey'    => $primaryKey,
                'form'          => $form->createView()
            ));
        })
        ->bind('administration_edit');

        $app->match('/administration', function () use ($app) {
            if (is_null($app['silexcms.security']->getUsername())) {
                return $app->redirect($app['url_generator']->generate('administration_login'));
            }

            $tables = array_keys($app['silexcms.sets']);

            return new TransientResponse($app, $app['silexcms.template.loader']->load('administration/administration_hub.html.twig'), array('tables' => $tables));
        })
        ->bind('administration_hub');
    }
}
