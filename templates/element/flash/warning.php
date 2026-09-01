<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
$params['tone'] = 'border-amber-200 bg-amber-50 text-amber-900';
echo $this->element('flash/default', compact('message', 'params'));
