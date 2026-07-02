<?php

/**
 * BGP Sessions widget form view.
 *
 * @var CView $this
 * @var array $data
 */

(new CWidgetFormView($data))
    ->addField(new CWidgetFieldMultiSelectGroupView($data['fields']['groupids']))
    ->addField(new CWidgetFieldNumericBoxView($data['fields']['stale_after']))
    ->show();
