<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerXf0YqIT\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerXf0YqIT/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerXf0YqIT.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerXf0YqIT\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \ContainerXf0YqIT\srcApp_KernelDevDebugContainer([
    'container.build_hash' => 'Xf0YqIT',
    'container.build_id' => '43dc81f4',
    'container.build_time' => 1565388340,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerXf0YqIT');
