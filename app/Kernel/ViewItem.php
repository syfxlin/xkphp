<?php

namespace App\Kernel;

class ViewItem
{
    protected $view = '';
    protected $data = [];

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
        extract($this->data);
        include $this->getView($this->view);
        $content = ob_get_clean();
        ob_end_clean();
        return $content;
    }

    private function getView($view)
    {
        $view = str_replace('.', '/', $view);
        $view_file = __DIR__ . "/../Views/$view.php";
        if (!file_exists($view_file)) {
            throw new \UnexpectedValueException("视图 $view_file 不存在");
        }
        return $view_file;
    }
}
