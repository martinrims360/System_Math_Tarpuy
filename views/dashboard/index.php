<?php // views/dashboard/index.php ?>

<!-- Stats -->
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon" style="background:#EEEDFE;color:#534AB7">
      <i class="ti ti-book"></i>
    </div>
    <div class="stat-body">
      <div class="n"><?= $stats['temas'] ?></div>
      <div class="l">Temas registrados</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#E1F5EE;color:#1D9E75">
      <i class="ti ti-users"></i>
    </div>
    <div class="stat-body">
      <div class="n"><?= $stats['docentes'] ?></div>
      <div class="l">Docentes activos</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#FAEEDA;color:#D48806">
      <i class="ti ti-users-group"></i>
    </div>
    <div class="stat-body">
      <div class="n"><?= $stats['grupos'] ?></div>
      <div class="l">Grupos activos</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#E6F1FB;color:#0C447C">
      <i class="ti ti-calendar"></i>
    </div>
    <div class="stat-body">
      <div class="n"><?= $stats['horarios'] ?></div>
      <div class="l">Sesiones programadas</div>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

  <!-- Últimos temas -->
  <div class="card" style="margin-bottom:0">
    <div class="card-title">
      <i class="ti ti-clock" style="color:#534AB7;vertical-align:-2px;margin-right:6px"></i>
      Últimos temas registrados
    </div>

    <?php if (empty($ultimos)): ?>
      <div class="empty-state" style="padding:24px">
        <i class="ti ti-book-off"></i>
        <p>Sin temas registrados aún</p>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:0">
        <?php foreach ($ultimos as $t): ?>
          <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #F1F3F5">
            <div style="width:38px;height:38px;border-radius:10px;background:#EEEDFE;
                        color:#534AB7;display:grid;place-items:center;font-size:16px;flex-shrink:0">
              <i class="ti ti-math"></i>
            </div>
            <div style="min-width:0;flex:1">
              <div style="font-size:13.5px;font-weight:500;color:#212529;
                          white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= htmlspecialchars($t['tema']) ?>
              </div>
              <div style="font-size:12px;color:#ADB5BD;margin-top:2px">
                <?= htmlspecialchars($t['docente']) ?> ·
                <?= htmlspecialchars($t['grupo'])   ?> ·
                <?= htmlspecialchars($t['area'])    ?> ·
                <?= date('d/m/Y', strtotime($t['fecha'])) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="margin-top:12px">
        <a href="/index.php?page=temas" class="btn btn-sm">
          <i class="ti ti-list"></i> Ver todos los temas
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Próximas sesiones -->
  <div class="card" style="margin-bottom:0">
    <div class="card-title">
      <i class="ti ti-calendar-event" style="color:#1D9E75;vertical-align:-2px;margin-right:6px"></i>
      Próximas sesiones
    </div>

    <?php if (empty($proximas)): ?>
      <div class="empty-state" style="padding:24px">
        <i class="ti ti-calendar-off"></i>
        <p>Sin sesiones programadas</p>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:0">
        <?php foreach ($proximas as $s): ?>
          <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #F1F3F5">
            <div style="width:38px;height:38px;border-radius:10px;background:#E1F5EE;
                        color:#1D9E75;display:grid;place-items:center;font-size:16px;flex-shrink:0">
              <i class="ti ti-clock"></i>
            </div>
            <div style="min-width:0;flex:1">
              <div style="font-size:13.5px;font-weight:500;color:#212529">
                <?= htmlspecialchars($s['docente']) ?>
                <span style="font-weight:400;color:#ADB5BD">·</span>
                <?= htmlspecialchars($s['grupo']) ?>
              </div>
              <div style="font-size:12px;color:#ADB5BD;margin-top:2px">
                <?= htmlspecialchars($s['dia_semana']) ?>
                <?= date('d/m/Y', strtotime($s['fecha'])) ?> ·
                <?= substr($s['hora_inicio'],0,5) ?>–<?= substr($s['hora_fin'],0,5) ?> ·
                <?= htmlspecialchars($s['salon']) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="margin-top:12px">
        <a href="/index.php?page=horarios" class="btn btn-sm">
          <i class="ti ti-calendar"></i> Ver todos los horarios
        </a>
      </div>
    <?php endif; ?>
  </div>

</div>

<!-- Accesos rápidos -->
<div class="card" style="margin-top:20px">
  <div class="card-title">Accesos rápidos</div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <a href="/index.php?page=temas&action=create" class="btn btn-primary">
      <i class="ti ti-plus"></i> Registrar tema
    </a>
    <a href="/index.php?page=horarios&action=create" class="btn">
      <i class="ti ti-calendar-plus"></i> Agregar horario
    </a>
    <a href="/index.php?page=seguimiento" class="btn">
      <i class="ti ti-chart-bar"></i> Ver seguimiento
    </a>
    <?php if (Auth::isCoord()): ?>
    <a href="/index.php?page=docentes&action=create" class="btn">
      <i class="ti ti-user-plus"></i> Nuevo docente
    </a>
    <?php endif; ?>
  </div>
</div>