<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerMxrQS5h\srcApp_KernelTestDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerMxrQS5h/srcApp_KernelTestDebugContainer.php') {
    touch(__DIR__.'/ContainerMxrQS5h.legacy');

    return;
}

if (!\class_exists(srcApp_KernelTestDebugContainer::class, false)) {
    \class_alias(\ContainerMxrQS5h\srcApp_KernelTestDebugContainer::class, srcApp_KernelTestDebugContainer::class, false);
}

return new \ContainerMxrQS5h\srcApp_KernelTestDebugContainer([
    'container.build_hash' => 'MxrQS5h',
    'container.build_id' => '650684fd',
    'container.build_time' => 1564961790,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerMxrQS5h');
