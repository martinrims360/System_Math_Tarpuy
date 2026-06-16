<?php // views/horarios/index.php

$vista = $_GET['vista'] ?? 'semanal';

// Colores por docente (rotativo)
$paleta = [
    ['bg'=>'#EEEDFE','border'=>'#C4C0F5','text'=>'#3C3489'],
    ['bg'=>'#E1F5EE','border'=>'#A3DFC7','text'=>'#085041'],
    ['bg'=>'#FAEEDA','border'=>'#F5D89A','text'=>'#633806'],
    ['bg'=>'#FAECE7','border'=>'#F5C4B0','text'=>'#712B13'],
    ['bg'=>'#E6F1FB','border'=>'#A8CFF0','text'=>'#0C447C'],
];
$docenteColor = [];
$ci = 0;
foreach ($horariosList as $h) {
    if (!isset($docenteColor[$h['docente']])) {
        $docenteColor[$h['docente']] = $paleta[$ci % count($paleta)];
        $ci++;
    }
}
?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-calendar" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Horarios</h1>
    <p>Sesiones programadas por docente, grupo y salón</p>
  </div>
  <a href="index.php?page=horarios&action=create" class="btn btn-primary">
    <i class="ti ti-plus"></i> Nuevo horario
  </a>
</div>

<!-- Tabs vista -->
<div style="display:flex;gap:4px;border-bottom:1px solid #E9ECEF;margin-bottom:20px">
  <a href="index.php?page=horarios&vista=semanal<?= http_build_query(array_diff_key($_GET,['vista'=>'','page'=>''])) ? '&'.http_build_query(array_diff_key($_GET,['vista'=>'','page'=>''])) : '' ?>"
     style="padding:8px 16px;font-size:14px;text-decoration:none;border-bottom:2px solid <?= $vista==='semanal' ? '#534AB7' : 'transparent' ?>;
            color:<?= $vista==='semanal' ? '#534AB7' : '#6C757D' ?>;font-weight:<?= $vista==='semanal' ? '500' : '400' ?>;margin-bottom:-1px">
    <i class="ti ti-calendar-week" style="vertical-align:-2px;margin-right:4px"></i>Vista semanal
  </a>
  <a href="index.php?page=horarios&vista=lista<?= http_build_query(array_diff_key($_GET,['vista'=>'','page'=>''])) ? '&'.http_build_query(array_diff_key($_GET,['vista'=>'','page'=>''])) : '' ?>"
     style="padding:8px 16px;font-size:14px;text-decoration:none;border-bottom:2px solid <?= $vista==='lista' ? '#534AB7' : 'transparent' ?>;
            color:<?= $vista==='lista' ? '#534AB7' : '#6C757D' ?>;font-weight:<?= $vista==='lista' ? '500' : '400' ?>;margin-bottom:-1px">
    <i class="ti ti-list" style="vertical-align:-2px;margin-right:4px"></i>Listado
  </a>
</div>

<!-- Filtros -->
<div class="card" style="padding:14px 18px;margin-bottom:18px">
  <form method="GET" action="index.php" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <input type="hidden" name="page"  value="horarios">
    <input type="hidden" name="vista" value="<?= htmlspecialchars($vista) ?>">

    <?php if (Auth::isCoord()): ?>
    <div style="display:flex;flex-direction:column;gap:4px">
      <label style="font-size:12px;color:#ADB5BD;font-weight:500">Docente</label>
      <select name="id_docente" style="padding:7px 10px;font-size:13px;border:1px solid #DEE2E6;border-radius:8px;background:#fff">
        <option value="">Todos</option>
        <?php foreach ($docentes as $d): ?>
          <option value="<?= $d['id_docente'] ?>" <?= ($_GET['id_docente']??'')==$d['id_docente']?'selected':'' ?>>
            <?= htmlspecialchars($d['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>

    <div style="display:flex;flex-direction:column;gap:4px">
      <label style="font-size:12px;color:#ADB5BD;font-weight:500">Grupo</label>
      <select name="id_grupo" style="padding:7px 10px;font-size:13px;border:1px solid #DEE2E6;border-radius:8px;background:#fff">
        <option value="">Todos</option>
        <?php foreach ($grupos as $g): ?>
          <option value="<?= $g['id_grupo'] ?>" <?= ($_GET['id_grupo']??'')==$g['id_grupo']?'selected':'' ?>>
            <?= htmlspecialchars($g['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:4px">
      <label style="font-size:12px;color:#ADB5BD;font-weight:500">Día</label>
      <select name="dia_semana" style="padding:7px 10px;font-size:13px;border:1px solid #DEE2E6;border-radius:8px;background:#fff">
        <option value="">Todos</option>
        <?php foreach ($dias as $d): ?>
          <option value="<?= $d ?>" <?= ($_GET['dia_semana']??'')===$d?'selected':'' ?>>
            <?= $d ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="display:flex;gap:8px;margin-top:auto">
      <button type="submit" class="btn btn-primary btn-sm">
        <i class="ti ti-filter"></i> Filtrar
      </button>
      <a href="index.php?page=horarios&vista=<?= $vista ?>" class="btn btn-sm">
        <i class="ti ti-x"></i> Limpiar
      </a>
    </div>
  </form>
</div>

<?php if ($vista === 'semanal'): ?>
<!-- ── VISTA SEMANAL ─────────────────────────────────── -->
<?php if (empty($horariosList)): ?>
  <div class="card">
    <div class="empty-state">
      <i class="ti ti-calendar-off"></i>
      <p>No hay horarios registrados.</p>
    </div>
  </div>
<?php else: ?>

<!-- Leyenda docentes -->
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
  <?php foreach ($docenteColor as $nombre => $c): ?>
    <span style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;
                 padding:4px 12px;border-radius:20px;background:<?= $c['bg'] ?>;color:<?= $c['text'] ?>;
                 border:1px solid <?= $c['border'] ?>">
      <span style="width:8px;height:8px;border-radius:50%;background:<?= $c['text'] ?>"></span>
      <?= htmlspecialchars($nombre) ?>
    </span>
  <?php endforeach; ?>
</div>

<div style="overflow-x:auto">
  <table style="width:100%;border-collapse:separate;border-spacing:3px;min-width:700px">
    <thead>
      <tr>
        <th style="width:70px;padding:8px 6px;font-size:12px;color:#ADB5BD;font-weight:500;text-align:center"></th>
        <?php foreach ($dias as $dia): ?>
          <th style="padding:8px 6px;font-size:13px;font-weight:500;color:#495057;
                     text-align:center;background:#F8F9FA;border-radius:8px">
            <?= $dia ?>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php
      $horas = ['07:00','08:00','09:00','10:00','11:00','12:00',
                '13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00'];
      foreach ($horas as $hora):
        $tieneAlgo = false;
        foreach ($dias as $dia) {
          foreach ($horariosDia[$dia] as $h) {
            if (substr($h['hora_inicio'],0,5) === $hora) { $tieneAlgo = true; break 2; }
          }
        }
      ?>
      <tr>
        <td style="text-align:right;padding:4px 8px 4px 0;font-size:12px;color:#ADB5BD;
                   vertical-align:top;padding-top:8px;white-space:nowrap">
          <?= $hora ?>
        </td>
        <?php foreach ($dias as $dia):
          $bloques = array_filter($horariosDia[$dia], fn($h) => substr($h['hora_inicio'],0,5) === $hora);
        ?>
        <td style="vertical-align:top;padding:2px;min-width:110px;min-height:44px">
          <?php foreach ($bloques as $b):
            $c = $docenteColor[$b['docente']] ?? $paleta[0];
          ?>
            <div style="background:<?= $c['bg'] ?>;border:1px solid <?= $c['border'] ?>;
                        border-radius:8px;padding:6px 8px;margin-bottom:3px;
                        cursor:pointer;transition:opacity .15s"
                 title="<?= htmlspecialchars($b['docente'].' · '.$b['grupo'].' · '.$b['salon']) ?>"
                 onclick="window.location='/index.php?page=horarios&action=edit&id=<?= $b['id_horario'] ?>'">
              <div style="font-size:12px;font-weight:600;color:<?= $c['text'] ?>;
                          white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= htmlspecialchars(explode(' ',$b['docente'])[0]) ?>
              </div>
              <div style="font-size:11px;color:<?= $c['text'] ?>;opacity:.8;
                          white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= substr($b['hora_inicio'],0,5) ?>–<?= substr($b['hora_fin'],0,5) ?>
              </div>
              <div style="font-size:11px;color:<?= $c['text'] ?>;opacity:.75;
                          white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= htmlspecialchars($b['salon']) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </td>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php else: ?>
<!-- ── VISTA LISTA ───────────────────────────────────── -->
<?php if (empty($horariosList)): ?>
  <div class="card">
    <div class="empty-state">
      <i class="ti ti-calendar-off"></i>
      <p>No hay horarios registrados.</p>
    </div>
  </div>
<?php else: ?>
  <div class="table-wrap">
    <table class="sys-table">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Día</th>
          <th>Horario</th>
          <th>Docente</th>
          <th>Grupo</th>
          <th>Salón</th>
          <th>Observaciones</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($horariosList as $h):
          $c = $docenteColor[$h['docente']] ?? $paleta[0];
        ?>
          <tr>
            <td style="font-size:13px;white-space:nowrap">
              <?= date('d/m/Y', strtotime($h['fecha'])) ?>
            </td>
            <td>
              <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:12px;
                           font-weight:500;background:<?= $c['bg'] ?>;color:<?= $c['text'] ?>">
                <?= htmlspecialchars($h['dia_semana']) ?>
              </span>
            </td>
            <td style="white-space:nowrap;font-size:13px;font-weight:500">
              <?= substr($h['hora_inicio'],0,5) ?> – <?= substr($h['hora_fin'],0,5) ?>
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <div style="width:28px;height:28px;border-radius:50%;
                            background:<?= $c['bg'] ?>;color:<?= $c['text'] ?>;
                            display:grid;place-items:center;font-size:11px;font-weight:600;flex-shrink:0">
                  <?= strtoupper(substr($h['docente'],0,2)) ?>
                </div>
                <span style="font-size:13px"><?= htmlspecialchars($h['docente']) ?></span>
              </div>
            </td>
            <td style="font-size:13px;font-weight:500"><?= htmlspecialchars($h['grupo']) ?></td>
            <td style="font-size:13px">
              <?= htmlspecialchars($h['salon']) ?>
              <?php if ($h['ubicacion']): ?>
                <div style="font-size:11.5px;color:#ADB5BD"><?= htmlspecialchars($h['ubicacion']) ?></div>
              <?php endif; ?>
            </td>
            <td style="font-size:12.5px;color:#495057;max-width:160px">
              <?= $h['observaciones']
                  ? htmlspecialchars(mb_substr($h['observaciones'],0,50)).(mb_strlen($h['observaciones'])>50?'…':'')
                  : '<span style="color:#ADB5BD">—</span>' ?>
            </td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="index.php?page=horarios&action=edit&id=<?= $h['id_horario'] ?>"
                   class="btn btn-sm" title="Editar">
                  <i class="ti ti-edit"></i>
                </a>
                <button class="btn btn-sm btn-danger" title="Eliminar"
                        onclick="confirmDelete(
                          'index.php?page=horarios&action=delete&id=<?= $h['id_horario'] ?>',
                          'este horario')">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
<?php endif; ?>