<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
$params['tone'] = 'border-navy-200 bg-navy-50 text-navy-800';
echo $this->element('flash/default', compact('message', 'params'));
