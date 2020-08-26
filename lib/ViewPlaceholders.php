<?php

namespace Wireframe;

/**
 * Container for View Placeholders
 *
 * @version 0.4.1
 * @author Teppo Koivula <teppo@wireframe-framework.com>
 * @license Mozilla Public License v2.0 https://mozilla.org/MPL/2.0/
 */
class ViewPlaceholders {

    /**
     * View instance
     *
     * @var View
     */
    protected $view;

    /**
     * Container for data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructor method
     *
     * @param View $view View object.
     */
    public function __construct(View $view) {
        $this->view = $view;
    }

    /**
     * Generic getter method
     *
     * Return content from a named view placeholder, or markup generated by
     * rendering the page using a view file matching the placeholder name.
     *
     * @param string $key Name of a view placeholder or view
     * @return mixed Content stored in a view placeholder, or rendered output of a view
     */
    public function __get(string $key) {
        $return = $this->data[$key] ?? null;
        if ($return === null && basename($key) === $key) {

            // params
            $page = $this->view->getPage();
            $file = $this->view->getViewFilename($key);

            if (\is_file($file)) {
                $page_layout = $page->getLayout();
                $page_view = $page->getView();
                $page->_wireframe_context = 'placeholder';
                $return = $page->setLayout('')->setView($key)->render();
                unset($page->_wireframe_context);
                if ($page_layout !== '') {
                    $page->setLayout($page_layout);
                }
                if ($page_view !== $key) {
                    $page->setView($page_view);
                }
            }
        }
        return $return;
    }

    /**
     * Check if a view placeholder or view file exists
     *
     * @param string $key Name of a view placeholder or view
     * @return bool
     */
    public function __isset(string $key): bool {
        $return = isset($this->data[$key]);
        if (!$return && basename($key) === $key) {
            $return = \is_file($this->view->getViewFilename($key));
        }
        return $return;
    }

    /**
     * Store values to the protected $data array
     *
     * @param string $key Name of a view placeholder
     * @param mixed $value Value to store in a view placeholder
     * @return ViewPlaceholders Current instance
     */
    public function __set(string $key, $value): ViewPlaceholders {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Check if View Placeholder value has been set.
     *
     * This method can be used to check if a value has already been set. Requesting a placeholder
     * value by calling `$placeholders->name` attempts to populate said placeholder automatically,
     * while this method returns boolean `false` unless said placeholder is already populated.
     *
     * @return bool
     */
    public function has(string $key): bool {
        return !empty($this->data[$key]);
    }

}
