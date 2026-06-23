<?php /** @var array $product */ ?>
<div class="col-md-4 col-sm-6">
    <div class="product-card h-100">
        <?php if (!empty($product['badge'])): ?>
            <span class="product-badge"><?= e($product['badge']) ?></span>
        <?php endif; ?>
        <div class="product-image-wrap">
            <img src="<?= e(image_src($product['image_url'] ?? '')) ?>" alt="<?= e($product['title']) ?>" class="product-image">
        </div>
        <div class="product-body">
            <small class="text-warning fw-semibold"><?= e(product_category_label($product['category'])) ?><?= !empty($product['game']) ? ' • ' . e($product['game']) : '' ?></small>
            <h5 class="mt-2 mb-2"><?= e($product['title']) ?></h5>
            <p class="text-soft small mb-3"><?= e($product['description']) ?></p>
            <div class="d-flex justify-content-between align-items-center gap-2 mt-auto">
                <strong class="price-text"><?= rupiah($product['price']) ?></strong>
                <a class="btn btn-warning btn-sm fw-bold" href="order.php?product_id=<?= (int) $product['id'] ?>">Order</a>
            </div>
        </div>
    </div>
</div>
