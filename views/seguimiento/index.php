<?php // views/seguimiento/index.php

$coloresArea  = ['#EEEDFE','#E1F5EE','#FAEEDA','#FAECE7','#E6F1FB'];
$coloresText  = ['#3C3489','#085041','#633806','#712B13','#0C447C'];
$totalTemas   = array_sum(array_column($porArea, 'total_temas'));
?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-chart-bar" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Seguimiento Temático</h1>
    <p>Resumen del avance de contenidos por grupo, docente y área</p>
  </div>
</div>

<!-- ── RESUMEN POR ÁREA ──────────────────────────────── -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:24px">
  <?php foreach ($porArea as $i => $a):
    $ci   = $i % count($coloresArea);
    $pct  = $totalTemas > 0 ? round($a['total_temas'] / $totalTemas * 100) : 0;
  ?>
    <div style="background:#fff;border:1px solid #E9ECEF;border-radius:12px;padding:16px 18px">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
        <div style="width:32px;height:32px;border-radius:8px;background:<?= $coloresArea[$ci] ?>;
                    color:<?= $coloresText[$ci] ?>;display:grid;place-items:center;font-size:15px;flex-shrink:0">
          <i class="ti ti-math"></i>
        </div>
        <span style="font-size:13px;font-weight:500;color:#212529">
          <?= htmlspecialchars($a['area']) ?>
        </span>
      </div>
      <div style="font-size:22px;font-weight:600;color:#212529;line-height:1">
        <?= $a['total_temas'] ?>
      </div>
      <div style="font-size:12px;color:#ADB5BD;margin-top:3px">temas · <?= $pct ?>% del total</div>
      <!-- barra de progreso -->
      <div style="height:4px;background:#F1F3F5;border-radius:4px;margin-top:10px;overflow:hidden">
        <div style="height:100%;width:<?= $pct ?>%;background:<?= $coloresText[$ci] ?>;border-radius:4px;
                    transition:width .4s"></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">

  <!-- ── AVANCE POR GRUPO ──────────────────────────── -->
  <div class="card" style="margin-bottom:0">
    <div class="card-title">
      <i class="ti ti-users-group" style="color:#534AB7;vertical-align:-2px;margin-right:6px"></i>
      Avance por grupo
    </div>
    <?php if (empty($porGrupo)): ?>
      <div class="empty-state" style="padding:20px">
        <i class="ti ti-users-group"></i><p>Sin datos</p>
      </div>
    <?php else: ?>
      <?php foreach ($porGrupo as $g): ?>
        <div style="padding:10px 0;border-bottom:1px solid #F1F3F5">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
            <div>
              <div style="font-size:13.5px;font-weight:500"><?= htmlspecialchars($g['grupo']) ?></div>
              <div style="font-size:12px;color:#ADB5BD;margin-top:2px">
                <?= $g['docentes_participantes'] ?> docente<?= $g['docentes_participantes']!=1?'s':'' ?> ·
                <?= $g['areas_cubiertas'] ?> área<?= $g['areas_cubiertas']!=1?'s':'' ?> trabajada<?= $g['areas_cubiertas']!=1?'s':'' ?>
                <?php if ($g['ultima_sesion']): ?>
                  · Última: <?= date('d/m/Y', strtotime($g['ultima_sesion'])) ?>
                <?php endif; ?>
              </div>
            </div>
            <span style="font-size:20px;font-weight:600;color:#534AB7;flex-shrink:0;margin-left:10px">
              <?= $g['total_temas'] ?>
            </span>
          </div>
          <!-- mini barra -->
          <?php $maxG = max(array_column($porGrupo,'total_temas')) ?: 1; ?>
          <div style="height:5px;background:#F1F3F5;border-radius:4px;overflow:hidden">
            <div style="height:100%;width:<?= round($g['total_temas']/$maxG*100) ?>%;
                        background:#534AB7;border-radius:4px"></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- ── AVANCE POR DOCENTE ────────────────────────── -->
  <div class="card" style="margin-bottom:0">
    <div class="card-title">
      <i class="ti ti-users" style="color:#1D9E75;vertical-align:-2px;margin-right:6px"></i>
      Participación por docente
    </div>
    <?php if (empty($porDocente)): ?>
      <div class="empty-state" style="padding:20px">
        <i class="ti ti-users"></i><p>Sin datos</p>
      </div>
    <?php else: ?>
      <?php
      $maxD = max(array_column($porDocente,'total_temas')) ?: 1;
      foreach ($porDocente as $d):
      ?>
        <div style="padding:10px 0;border-bottom:1px solid #F1F3F5">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
            <div style="width:30px;height:30px;border-radius:50%;background:#E1F5EE;
                        color:#1D9E75;display:grid;place-items:center;
                        font-size:11px;font-weight:600;flex-shrink:0">
              <?= strtoupper(substr($d['docente'],0,2)) ?>
            </div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13.5px;font-weight:500;
                          white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= htmlspecialchars($d['docente']) ?>
              </div>
              <div style="font-size:12px;color:#ADB5BD">
                <?= $d['grupos_atendidos'] ?> grupo<?= $d['grupos_atendidos']!=1?'s':'' ?> ·
                <?= $d['areas_trabajadas'] ?> área<?= $d['areas_trabajadas']!=1?'s':'' ?>
                <?php if ($d['ultima_sesion']): ?>
                  · Última: <?= date('d/m/Y', strtotime($d['ultima_sesion'])) ?>
                <?php endif; ?>
              </div>
            </div>
            <span style="font-size:18px;font-weight:600;color:#1D9E75;flex-shrink:0">
              <?= $d['total_temas'] ?>
            </span>
          </div>
          <div style="height:5px;background:#F1F3F5;border-radius:4px;overflow:hidden">
            <div style="height:100%;width:<?= round($d['total_temas']/$maxD*100) ?>%;
                        background:#1D9E75;border-radius:4px"></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<!-- ── HISTORIAL CON FILTROS ─────────────────────────── -->
<div class="card">
  <div class="card-title">
    <i class="ti ti-history" style="color:#D48806;vertical-align:-2px;margin-right:6px"></i>
    Historial detallado
  </div>

  <!-- Filtros -->
  <form method="GET" action="/index.php"
        style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;
               margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #F1F3F5">
    <input type="hidden" name="page" value="seguimiento">

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
      <label style="font-size:12px;color:#ADB5BD;font-weight:500">Área</label>
      <select name="id_area" style="padding:7px 10px;font-size:13px;border:1px solid #DEE2E6;border-radius:8px;background:#fff">
        <option value="">Todas</option>
        <?php foreach ($areas as $a): ?>
          <option value="<?= $a['id_area'] ?>" <?= ($_GET['id_area']??'')==$a['id_area']?'selected':'' ?>>
            <?= htmlspecialchars($a['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:4px">
      <label style="font-size:12px;color:#ADB5BD;font-weight:500">Desde</label>
      <input type="date" name="fecha_desde" value="<?= htmlspecialchars($_GET['fecha_desde']??'') ?>"
             style="padding:7px 10px;font-size:13px;border:1px solid #DEE2E6;border-radius:8px">
    </div>

    <div style="display:flex;flex-direction:column;gap:4px">
      <label style="font-size:12px;color:#ADB5BD;font-weight:500">Hasta</label>
      <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($_GET['fecha_hasta']??'') ?>"
             style="padding:7px 10px;font-size:13px;border:1px solid #DEE2E6;border-radius:8px">
    </div>

    <div style="display:flex;gap:8px;margin-top:auto">
      <button type="submit" class="btn btn-primary btn-sm">
        <i class="ti ti-filter"></i> Filtrar
      </button>
      <a href="index.php?page=seguimiento" class="btn btn-sm">
        <i class="ti ti-x"></i> Limpiar
      </a>
    </div>
  </form>

  <?php if (empty($historial)): ?>
    <div class="empty-state" style="padding:28px">
      <i class="ti ti-history"></i>
      <p>No hay registros con estos filtros.</p>
    </div>
  <?php else: ?>
    <div style="font-size:13px;color:#ADB5BD;margin-bottom:10px">
      <?= count($historial) ?> registro<?= count($historial)!=1?'s':'' ?>
    </div>

    <!-- Agrupar por fecha -->
    <?php
    $porFecha = [];
    foreach ($historial as $r) {
        $porFecha[$r['fecha']][] = $r;
    }
    ?>

    <?php foreach ($porFecha as $fecha => $registros): ?>
      <div style="margin-bottom:20px">
        <!-- Encabezado de fecha -->
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
          <div style="background:#534AB7;color:#fff;border-radius:8px;
                      padding:4px 12px;font-size:12.5px;font-weight:500;white-space:nowrap">
            <?= date('d/m/Y', strtotime($fecha)) ?>
            — <?= ['Sunday'=>'Dom','Monday'=>'Lun','Tuesday'=>'Mar','Wednesday'=>'Mié',
                    'Thursday'=>'Jue','Friday'=>'Vie','Saturday'=>'Sáb'][date('l',strtotime($fecha))] ?>
          </div>
          <div style="flex:1;height:1px;background:#F1F3F5"></div>
          <span style="font-size:12px;color:#ADB5BD"><?= count($registros) ?> tema<?= count($registros)!=1?'s':'' ?></span>
        </div>

        <!-- Registros del día -->
        <div style="display:flex;flex-direction:column;gap:8px">
          <?php foreach ($registros as $i => $r):
            $ci = array_search($r['area'], array_column($areas,'nombre'));
            $ci = $ci !== false ? $ci % count($coloresArea) : 0;
          ?>
            <div style="display:flex;gap:12px;padding:12px 16px;
                        background:#F8F9FA;border-radius:10px;border:1px solid #F1F3F5">
              <!-- Área badge -->
              <div style="flex-shrink:0;padding-top:2px">
                <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11.5px;
                             font-weight:500;background:<?= $coloresArea[$ci] ?>;color:<?= $coloresText[$ci] ?>">
                  <?= htmlspecialchars($r['area']) ?>
                </span>
              </div>
              <!-- Contenido -->
              <div style="flex:1;min-width:0">
                <div style="font-size:14px;font-weight:500;color:#212529">
                  <?= htmlspecialchars($r['tema']) ?>
                  <?php if ($r['subtema']): ?>
                    <span style="font-weight:400;color:#6C757D;font-size:13px">
                      — <?= htmlspecialchars($r['subtema']) ?>
                    </span>
                  <?php endif; ?>
                </div>
                <div style="font-size:12.5px;color:#ADB5BD;margin-top:4px;
                            display:flex;gap:14px;flex-wrap:wrap">
                  <span><i class="ti ti-users" style="vertical-align:-2px;font-size:13px"></i>
                    <?= htmlspecialchars($r['grupo']) ?></span>
                  <span><i class="ti ti-user" style="vertical-align:-2px;font-size:13px"></i>
                    <?= htmlspecialchars($r['docente']) ?></span>
                  <?php if ($r['salon']): ?>
                    <span><i class="ti ti-door" style="vertical-align:-2px;font-size:13px"></i>
                      <?= htmlspecialchars($r['salon']) ?></span>
                  <?php endif; ?>
                </div>
                <?php if ($r['observaciones']): ?>
                  <div style="font-size:12.5px;color:#6C757D;margin-top:5px;
                              font-style:italic;border-left:3px solid #DEE2E6;padding-left:8px">
                    <?= htmlspecialchars($r['observaciones']) ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>