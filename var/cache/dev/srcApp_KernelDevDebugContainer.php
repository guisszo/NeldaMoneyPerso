<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerAr5LVai\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerAr5LVai/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerAr5LVai.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerAr5LVai\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \ContainerAr5LVai\srcApp_KernelDevDebugContainer([
    'container.build_hash' => 'Ar5LVai',
    'container.build_id' => '8c29df9c',
    'container.build_time' => 1567979776,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerAr5LVai');
