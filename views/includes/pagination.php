<?php if ($paginator->hasPages()): ?>
<div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
  <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
    <div>
      <p class="text-sm text-gray-700">
        Mostrando <span class="font-medium"><?= $paginator->getOffset() + 1 ?></span> a 
        <span class="font-medium"><?= min($paginator->getOffset() + $paginator->getLimit(), $paginator->getTotalItems()) ?></span> de 
        <span class="font-medium"><?= $paginator->getTotalItems() ?></span> resultados
      </p>
    </div>
    <div>
      <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        
        <?php
          // Construir URL base para mantener los parámetros GET
          $query = $_GET;
          unset($query['page']); // Lo removemos para reemplazar
        ?>

        <?php if ($paginator->hasPrevious()): ?>
          <?php $query['page'] = $paginator->getPreviousPage(); ?>
          <a href="?<?= http_build_query($query) ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
            <span class="sr-only">Anterior</span>
            <i class="fas fa-chevron-left"></i>
          </a>
        <?php endif; ?>

        <?php foreach ($paginator->getPageRange() as $page): ?>
          <?php $query['page'] = $page; ?>
          <?php if ($page == $paginator->getCurrentPage()): ?>
            <span class="relative inline-flex items-center px-4 py-2 border border-inventory bg-blue-50 text-sm font-medium text-inventory z-10">
              <?= $page ?>
            </span>
          <?php else: ?>
            <a href="?<?= http_build_query($query) ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
              <?= $page ?>
            </a>
          <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($paginator->hasNext()): ?>
          <?php $query['page'] = $paginator->getNextPage(); ?>
          <a href="?<?= http_build_query($query) ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
            <span class="sr-only">Siguiente</span>
            <i class="fas fa-chevron-right"></i>
          </a>
        <?php endif; ?>
      </nav>
    </div>
  </div>
</div>
<?php endif; ?>
