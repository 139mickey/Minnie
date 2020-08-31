<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ApiControllerEvent extends Event
{
    /**
     * The PRE_SUBMIT event is dispatched at the beginning of the Form::submit() method.
     *
     * It can be used to:
     *  - Change data from the request, before submitting the data to the api_controller.
     *  - Add or remove form fields, before submitting the data to the api_controller.
     *
     * @Event("Symfony\Component\Form\Event\PreSubmitEvent")
     */
    const PRE_POST_PUT = 'api_controller.pre_submit';

    /**
     * The SUBMIT event is dispatched after the Form::submit() method
     * has changed the view data by the request data, or submitted and mapped
     * the children if the form is compound, and after reverse transformation
     * to normalized representation.
     *
     * It's also dispatched just before the Form::submit() method transforms back
     * the normalized data to the model and view data.
     *
     * So at this stage children of compound forms are submitted and synchronized, unless
     * their transformation failed, but a parent would still be at the PRE_SUBMIT level.
     *
     * Since the current form is not synchronized yet, it is still possible to add and
     * remove fields.
     *
     * @Event("Symfony\Component\Form\Event\SubmitEvent")
     */
    const POST_PUT = 'api_controller.submit';

    /**
     * The FormEvents::POST_SUBMIT event is dispatched at the very end of the Form::submit().
     *
     * It this stage the model and view data may have been denormalized. Otherwise the form
     * is desynchronized because transformation failed during submission.
     *
     * It can be used to fetch data after denormalization.
     *
     * The event attaches the current view data. To know whether this is the renormalized data
     * or the invalid request data, call Form::isSynchronized() first.
     *
     * @Event("Symfony\Component\Form\Event\PostSubmitEvent")
     */
    const POST_POST_PUT = 'api_controller.post_submit';

    /**
     * The FormEvents::PRE_SET_DATA event is dispatched at the beginning of the Form::setData() method.
     *
     * It can be used to:
     *  - Modify the data given during pre-population;
     *  - Keep synchronized the form depending on the data (adding or removing fields dynamically).
     *
     * @Event("Symfony\Component\Form\Event\PreSetDataEvent")
     */
    const PRE_SET_DATA = 'api_controller.pre_set_data';

    /**
     * The FormEvents::POST_SET_DATA event is dispatched at the end of the Form::setData() method.
     *
     * This event can be used to modify the form depending on the final state of the underlying data
     * accessible in every representation: model, normalized and view.
     *
     * @Event("Symfony\Component\Form\Event\PostSetDataEvent")
     */
    const POST_SET_DATA = 'api_controller.post_set_data';

    protected $controller;
    protected $data;

    /**
     * @param $controller
     * @param mixed $data The data
     */
    public function __construct($controller, $data)
    {
        $this->controller = $controller;
        $this->data = $data;
    }

    /**
     * Returns the data associated with this event.
     *
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns the data associated with this event.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Allows updating with some filtered data.
     *
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
