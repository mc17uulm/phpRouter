<?php

namespace phpRouter;

use Closure;

/**
 * Class View
 * @package phpRouter
 */
class View {

    /**
     * @var Closure|null
     */
    protected ?Closure $children;

    /**
     * View constructor.
     * @param Closure|null $children
     */
    public function __construct(?Closure $children = null) {
        $this->children = $children;
    }

    /**
     * @return string
     *
     * Render $this View to a string
     */
    public function render() : string {
        ob_start();
        if($this->children !== null) {
            $stretch = $this->children;
            $stretch();
        }
        $content = ob_get_contents();
        ob_end_clean();
        return ($content === false) ? "" : $content;
    }

    /**
     * Echo $this View
     */
    public function show() : void {
        echo $this->render();
    }

    /**
     * @param View $view
     *
     * Show the given View
     */
    public static function print_view(View $view) : void {
        $view->show();
    }

}