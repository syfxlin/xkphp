<?php

namespace App\Kernel;

class View
{
    protected $view = '';
    protected $data = [];

    public function exists($view)
    {
        $view = str_replace('.', '/', $view);
        $view_file = __DIR__ . "/../Views/$view.php";
        return file_exists($view_file);
    }

    public function assign(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function with(string $key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function make(string $view, array $data = [])
    {
        $this->view = $view;
        $this->assign($data);
        return $this;
    }

    public function render()
    {
        ob_start();
        extract($this->pushCommon());
        extract($this->data);
        include $this->getView($this->view);
        $content = ob_get_clean();
        ob_end_clean();
        return $content;
    }

    private function getView($view)
    {
        $view_file = view_path($view);
        if (!file_exists($view_file)) {
            throw new \UnexpectedValueException("The view $view_file does not exist");
        }
        return $view_file;
    }

    private function pushCommon()
    {
        return [
            'csrf' => '<input type="hidden" name="_token" value="' . csrf_token() . '">',
            'csrf_token' => csrf_token(),
            'request' => request(),
            'include' => function ($view) {
                include view_path($view);
            },
            'echo' => function ($content) {
                echo $content;
            },
            'json' => function ($data, $options = 0) {
                echo json_encode($data, $options);
            },
            'asset' => function ($asset) {
                echo asset($asset);
            }
        ];
    }
}
