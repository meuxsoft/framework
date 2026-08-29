<?php

namespace Core;

use Core\Libraries\Layout\Layout;
use Core\Libraries\Request\Request;
use Core\Libraries\Security\Security;
use Core\Libraries\Session\Session;

class Controller
{
    /**
     * @var string|null
     */
    protected $layout = 'main';

    /**
     * Set the active layout for subsequent views.
     *
     * @param string|null $layout Layout name (e.g. 'main', 'auth', 'admin') or null for no layout
     * @return $this
     */
    protected function layout(?string $layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Render a view wrapped in a layout.
     *
     * @param string $view
     * @param array $data
     * @param string|null $layout
     * @return string
     */
    protected function view(string $view, array $data = [], ?string $layout = null)
    {
        $resolvedLayout = $layout !== null ? $layout : $this->layout;
        return Layout::render($resolvedLayout, $view, $data);
    }

    /**
     * Return a JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return void
     */
    protected function json($data, int $status = 200, array $headers = []): void
    {
        Layout::json($data, $status, $headers);
    }

    /**
     * Redirect to a specific URL or path.
     *
     * @param string $url
     * @param int $status
     * @return void
     */
    protected function redirect(string $url, int $status = 302): void
    {
        redirect($url, $status);
    }

    /**
     * Redirect back to previous page with optional flash messages.
     *
     * @param string|null $error
     * @param string|null $success
     * @return void
     */
    protected function back(?string $error = null, ?string $success = null): void
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

    /**
     * Validate incoming data and automatically flash errors and old input on failure.
     *
     * @param array $data
     * @param array $rules
     * @return array Returns sanitized data if valid, redirects back if invalid
     */
    protected function validate(array $data, array $rules): array
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
