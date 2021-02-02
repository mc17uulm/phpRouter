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
     */
    public function render() : string {
        ob_start();
        if($this->children !== null) {
            $canvas = $this->children;
            $canvas();
        }
        $content = ob_get_contents();
        ob_end_clean();
        return ($content === false) ? "" : $content;
    }

    public function show() : void {
        echo $this->render();
    }

    /**
     * @param Closure $canvas
     * @return string
     */
    public static function to_view(Closure $canvas) : string {
        return (new View($canvas))->render();
    }

}