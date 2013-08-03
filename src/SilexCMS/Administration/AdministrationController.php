<?php

namespace SilexCMS\Administration;

use Silex\Application;

use SilexCMS\Form\Form;
use SilexCMS\Form\TableType;
use SilexCMS\Response\TransientResponse;

use Symfony\Component\HttpFoundation\Request;
use SilexCMS\Repository\GenericRepository;
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
                return $app->redirect($app['url_generator']->generate('index'));
            }

            $repository = new GenericRepository($this->db, $table);
            $schema = $repository->getSchema();
            $rows = $repository->findAll(true);

            foreach ($rows as $row) {
                $data[] = array_map(function($val) {
                    return is_string($val) && strlen($val) > 50 ? substr(strip_tags($val), 0, 47) . '...' : $val;
                }, $row);
            }

            return new TransientResponse($app, $app['silexcms.template.loader']->load('administration/administration_table.html.twig'), array('table' => $table, 'fields' => $schema, 'rows' => $data));
        })
        ->bind('administration_table');

        $app->match('/administration/{table}/{id}', function (Request $req, $table, $id) use ($app) {

            if (is_null($app['silexcms.security']->getUsername())) {
                return $app->redirect($app['url_generator']->generate('index'));
            }

            if (!is_numeric($id) && 'new' !== $id) {
                throw new \Exception("Wrong parameters");
            }

            $repository = new GenericRepository($this->db, $table);
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

            return new TransientResponse($app, $app['silexcms.template.loader']->load('administration/administration_edit.html.twig'), array(
                'table' => $table,
                'id'    => $id,
                'form'  => $form->createView()
            ));
        })
        ->bind('administration_edit');

        $app->match('/administration', function () use ($app) {
            if (is_null($app['silexcms.security']->getUsername())) {
                return $app->redirect($app['url_generator']->generate('index'));
            }

            $listTables = array();
            try {
                $tables = $this->db->fetchAll('SHOW tables');
            } catch (\Exception $e) {
                $tables = array();
            }

            foreach ($tables as $table) {
                $listTables[] = array_shift($table);
            }

            return new TransientResponse($app, $app['silexcms.template.loader']->load('administration/administration_hub.html.twig'), array('tables' => $listTables));
        })
        ->bind('administration_hub');
    }
}