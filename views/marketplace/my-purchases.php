<div style="padding-top: 9rem;">
    <div style="max-width: 100rem; margin: 0 auto; padding: 0 2rem;">
        <h1 style="font-size: 2.8rem; font-weight: 800; margin: 0 0 0.5rem;">Mis Compras</h1>
        <p style="color: #94A3B8; margin: 0 0 3rem;">Recursos que has adquirido</p>

        <?php if(!empty($transactions)): ?>
            <?php foreach($transactions as $tx): ?>
                <div class="review-card" style="display: flex; align-items: center; justify-content: space-between; gap: 2rem;">
                    <div style="display: flex; align-items: center; gap: 1.5rem; flex: 1;">
                        <div style="width: 5rem; height: 5rem; display:flex; align-items:center; justify-content:center; background: rgba(16,185,129,0.1); border-radius: 0.75rem; flex-shrink: 0;">
                            <i class="fa-solid fa-file" style="color: #10B981; font-size: 2rem;"></i>
                        </div>
                        <div>
                            <a href="/marketplace/asset?id=<?php echo $tx->asset_id; ?>" style="font-size: 1.5rem; font-weight: 600; color: #F1F5F9;">
                                <?php echo $tx->asset ? s($tx->asset->titulo) : 'Recurso #' . $tx->asset_id; ?>
                            </a>
                            <p style="font-size: 1.2rem; color: #64748B; margin: 0.3rem 0 0;">
                                Vendedor: <?php echo $tx->vendedor ? s($tx->vendedor->nombre . ' ' . $tx->vendedor->apellido) : 'N/A'; ?>
                                · Estado: <span style="color: <?php echo $tx->estado === 'completada' ? '#22C55E' : '#F59E0B'; ?>;"><?php echo s(ucfirst($tx->estado)); ?></span>
                            </p>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.8rem; font-weight: 700; color: #10B981;">
                            <?php echo $tx->monto > 0 ? '$' . number_format($tx->monto, 2) : 'Gratis'; ?>
                        </div>
                        <?php if($tx->estado === 'completada' && $tx->asset): ?>
                            <a href="/uploads/assets/<?php echo s($tx->asset->archivo); ?>" download 
                               style="font-size: 1.2rem; color: #CCFF00; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <i class="fa-solid fa-download"></i> Descargar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon"><i class="fa-solid fa-bag-shopping"></i></div>
                <h3 class="empty-state__title">No has comprado recursos aún</h3>
                <p class="empty-state__text">Explora el marketplace y descubre recursos útiles para tu carrera</p>
                <a href="/marketplace" class="empty-state__action"><i class="fa-solid fa-store"></i> Ir al Marketplace</a>
            </div>
        <?php endif; ?>
    </div>
</div>
