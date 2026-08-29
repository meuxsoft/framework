<?php

namespace Core;

use Core\Libraries\Layout\Layout;
use Core\Libraries\Request\Request;
use Core\Libraries\Security\Security;
use Core\Libraries\Session\Session;

class Controller
{
    protected $layout = 'main';

    protected function layout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    protected function view($view, $data = [], $layout = null)
    {
        $resolvedLayout = $layout !== null ? $layout : $this->layout;

        // Eğer modül controller'ı içindeysek ve view adında '::' yoksa modülün kendi Views klasörüne otomatik bakar
        if (strpos($view, '::') === false) {
            $class = get_class($this);
            if (preg_match('/^App\\\\Modules\\\\([a-zA-Z0-9_]+)\\\\/', $class, $matches)) {
                $moduleName = $matches[1];
                $moduleViewFile = MODULES_PATH . '/' . $moduleName . '/Views/' . str_replace('.', '/', $view) . '.php';
                if (file_exists($moduleViewFile)) {
                    $view = $moduleName . '::' . $view;
                }
            }
        }

        return Layout::render($resolvedLayout, $view, $data);
    }

    protected function json($data, $status = 200, $headers = [])
    {
        Layout::json($data, $status, $headers);
    }

    protected function redirect($url, $status = 302)
    {
        redirect($url, $status);
    }

    protected function back($error = null, $success = null)
    {
        if ($error !== null) {
            Session::flash('error', $error);
        }
        if ($success !== null) {
            Session::flash('success', $success);
        }
        Request::flashInput();
        back();
    }

    protected function validate($data, $rules)
    {
        $errors = Security::validate($data, $rules);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Lütfen formdaki hataları kontrol ediniz.');
            Request::flashInput();
            back();
            exit;
        }

        return $data;
    }
}
