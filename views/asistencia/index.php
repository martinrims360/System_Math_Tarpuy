<?php // views/asistencia/index.php ?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-calendar-check" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Mi Asistencia</h1>
    <p>Tu asistencia se registra a través del formulario, no manualmente aquí</p>
  </div>
</div>

<!-- Enlace al Google Form -->
<div class="card" style="margin-bottom:20px;text-align:center;padding:28px 20px">
  <i class="ti ti-clipboard-check" style="font-size:34px;color:#534AB7"></i>
  <h3 style="margin:12px 0 6px;font-size:16px;color:#343A40">Marca tu asistencia de hoy</h3>
  <p style="color:#495057;font-size:13.5px;margin:0 0 16px">
    Completa el formulario con tu correo institucional. El sistema se actualiza automáticamente en unos segundos.
  </p>
  <a href="<?= htmlspecialchars($formUrl) ?>" target="_blank" rel="noopener" class="btn btn-primary">
    <i class="ti ti-external-link"></i> Abrir formulario de asistencia
  </a>
</div>

<!-- Estado de hoy (solo lectura) -->
<div class="card" style="margin-bottom:20px">
  <h3 style="margin:0 0 14px;font-size:15px;color:#343A40">
    <i class="ti ti-calendar-event" style="color:#534AB7;margin-right:6px"></i>
    Estado de hoy — <?= date('d/m/Y') ?>
  </h3>

  <?php if (!$hoy): ?>
    <div class="empty-state" style="padding:20px 0">
      <i class="ti ti-clock-hour-3"></i>
      <p>Aún no registras tu asistencia hoy. Usa el botón de arriba para completar el formulario.</p>
    </div>
  <?php else: $e = $estados[$hoy['estado']]; ?>
    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
      <span class="badge-status" style="background:<?= $e['bg'] ?>;color:<?= $e['text'] ?>;font-size:13px;padding:6px 14px">
        <?= $e['label'] ?>
      </span>
      <span style="color:#495057;font-size:13px">
        <i class="ti ti-clock-play" style="vertical-align:-2px"></i>
        Entrada: <?= $hoy['hora_entrada'] ? date('H:i', strtotime($hoy['hora_entrada'])) : '—' ?>
      </span>
      <span style="color:#495057;font-size:13px">
        <i class="ti ti-clock-stop" style="vertical-align:-2px"></i>
        Salida: <?= $hoy['hora_salida'] ? date('H:i', strtotime($hoy['hora_salida'])) : '—' ?>
      </span>
    </div>
    <?php if (!empty($hoy['observaciones'])): ?>
      <p style="margin:12px 0 0;color:#495057;font-size:12.5px">
        <i class="ti ti-note"></i> <?= htmlspecialchars($hoy['observaciones']) ?>
      </p>
    <?php endif; ?>
  <?php endif; ?>
</div>

<!-- Resumen del mes -->
<div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
  <span class="badge-status active">Asistencias: <?= $resumen['asistencias'] ?></span>
  <span class="badge-status" style="background:#FEF3C7;color:#92400E">Tardanzas: <?= $resumen['tardanzas'] ?></span>
  <span class="badge-status" style="background:#FEE2E2;color:#991B1B">Faltas: <?= $resumen['faltas'] ?></span>
  <span class="badge-status" style="background:#EDE9FE;color:#5B21B6">Justificados: <?= $resumen['justificados'] ?></span>
</div>

<!-- Filtro mes/año -->
<?php
  $mesesNombres = [1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio',
                   'Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  
  // Asegurar que mes y año tengan valores por defecto
  if (!isset($mes)) $mes = 9;
  if (!isset($anio)) $anio = 2026;
?>
<form action="index.php" method="GET" style="display:flex;gap:10px;align-items:end;margin-bottom:14px;flex-wrap:wrap">
  <input type="hidden" name="page" value="asistencia">
  <div>
    <label style="font-size:12.5px;color:#495057;font-weight:500">Mes</label>
    <select name="mes" class="form-control">
      <?php foreach ($mesesNombres as $num => $nombre): ?>
        <option value="<?= $num ?>" <?= $num == $mes ? 'selected' : '' ?>><?= $nombre ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label style="font-size:12.5px;color:#495057;font-weight:500">Año</label>
    <input type="number" name="anio" class="form-control" style="width:100px" value="<?= $anio ?>">
  </div>
  <button type="submit" class="btn btn-sm">
    <i class="ti ti-filter"></i> Filtrar
  </button>
</form>

<!-- Historial -->
<?php if (empty($historial)): ?>
  <div class="card">
    <div class="empty-state">
      <i class="ti ti-calendar-off"></i>
      <p>No hay registros de asistencia para este período.</p>
    </div>
  </div>

<?php else: ?>
  <div class="table-wrap">
    <table class="sys-table">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Entrada</th>
          <th>Salida</th>
          <th>Estado</th>
          <th>Observaciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($historial as $h): $e = $estados[$h['estado']]; ?>
          <tr>
            <td><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
            <td><?= $h['hora_entrada'] ? date('H:i', strtotime($h['hora_entrada'])) : '—' ?></td>
            <td><?= $h['hora_salida']  ? date('H:i', strtotime($h['hora_salida']))  : '—' ?></td>
            <td>
              <span class="badge-status" style="background:<?= $e['bg'] ?>;color:<?= $e['text'] ?>">
                <?= $e['label'] ?>
              </span>
            </td>
            <td style="color:#495057;font-size:12.5px"><?= htmlspecialchars($h['observaciones'] ?: '—') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>