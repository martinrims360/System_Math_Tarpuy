<?php // views/temas/index.php ?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-book" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Registro de Temas</h1>
    <p>Historial de contenidos desarrollados en sesiones</p>
  </div>
  <a href="index.php?page=temas&action=create" class="btn btn-primary">
    <i class="ti ti-plus"></i> Registrar tema
  </a>
</div>

<!-- Filtros -->
<div class="card" style="padding:16px 20px;margin-bottom:16px">
  <form method="GET" action="index.php" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <input type="hidden" name="page" value="temas">

    <?php if (Auth::isCoord()): ?>
    <div style="display:flex;flex-direction:column;gap:4px">
      <label style="font-size:12px;color:#ADB5BD;font-weight:500">Docente</label>
      <select name="id_docente" style="padding:7px 10px;font-size:13px;border:1px solid #DEE2E6;border-radius:8px;background:#fff">
        <option value="">Todos</option>
        <?php foreach ($docentes as $d): ?>
          <option value="<?= $d['id_docente'] ?>"
            <?= ($_GET['id_docente'] ?? '') == $d['id_docente'] ? 'selected' : '' ?>>
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
          <option value="<?= $g['id_grupo'] ?>"
            <?= ($_GET['id_grupo'] ?? '') == $g['id_grupo'] ? 'selected' : '' ?>>
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
          <option value="<?= $a['id_area'] ?>"
            <?= ($_GET['id_area'] ?? '') == $a['id_area'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($a['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:4px">
      <label style="font-size:12px;color:#ADB5BD;font-weight:500">Desde</label>
      <input type="date" name="fecha_desde" value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>"
             style="padding:7px 10px;font-size:13px;border:1px solid #DEE2E6;border-radius:8px">
    </div>

    <div style="display:flex;flex-direction:column;gap:4px">
      <label style="font-size:12px;color:#ADB5BD;font-weight:500">Hasta</label>
      <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>"
             style="padding:7px 10px;font-size:13px;border:1px solid #DEE2E6;border-radius:8px">
    </div>

    <div style="display:flex;gap:8px;margin-top:auto">
      <button type="submit" class="btn btn-primary btn-sm">
        <i class="ti ti-filter"></i> Filtrar
      </button>
      <a href="index.php?page=temas" class="btn btn-sm">
        <i class="ti ti-x"></i> Limpiar
      </a>
    </div>
  </form>
</div>

<?php if (empty($temas)): ?>
  <div class="card">
    <div class="empty-state">
      <i class="ti ti-book-off"></i>
      <p>No hay temas registrados con estos filtros.</p>
      <a href="index.php?page=temas&action=create" class="btn btn-primary" style="margin-top:14px">
        <i class="ti ti-plus"></i> Registrar primer tema
      </a>
    </div>
  </div>
<?php else: ?>

  <div style="font-size:13px;color:#ADB5BD;margin-bottom:10px">
    <?= count($temas) ?> registro<?= count($temas) != 1 ? 's' : '' ?> encontrado<?= count($temas) != 1 ? 's' : '' ?>
  </div>

  <?php
  $coloresArea = ['#EEEDFE','#E1F5EE','#FAEEDA','#FAECE7','#E6F1FB'];
  $coloresText = ['#3C3489','#085041','#633806','#712B13','#0C447C'];
  $areaMap = [];
  foreach ($areas as $i => $a) {
    $areaMap[$a['nombre']] = $i % count($coloresArea);
  }
  ?>

  <div class="table-wrap">
    <table class="sys-table">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Docente</th>
          <th>Grupo</th>
          <th>Área</th>
          <th>Tema / Subtema</th>
          <th>Salón</th>
          <th>Observaciones</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($temas as $t):
          $ci = $areaMap[$t['area']] ?? 0;
        ?>
          <tr>
            <td style="white-space:nowrap;font-size:13px">
              <?= date('d/m/Y', strtotime($t['fecha'])) ?>
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <div style="width:28px;height:28px;border-radius:50%;background:#EEEDFE;
                            color:#534AB7;display:grid;place-items:center;
                            font-size:11px;font-weight:600;flex-shrink:0">
                  <?= strtoupper(substr($t['docente'],0,2)) ?>
                </div>
                <span style="font-size:13px"><?= htmlspecialchars($t['docente']) ?></span>
              </div>
            </td>
            <td>
              <span style="font-size:13px;font-weight:500">
                <?= htmlspecialchars($t['grupo']) ?>
              </span>
            </td>
            <td>
              <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:12px;
                           font-weight:500;background:<?= $coloresArea[$ci] ?>;color:<?= $coloresText[$ci] ?>">
                <?= htmlspecialchars($t['area']) ?>
              </span>
            </td>
            <td style="max-width:220px">
              <div style="font-size:13.5px;font-weight:500;color:#212529">
                <?= htmlspecialchars($t['tema']) ?>
              </div>
              <?php if ($t['subtema']): ?>
                <div style="font-size:12px;color:#ADB5BD;margin-top:2px">
                  <?= htmlspecialchars($t['subtema']) ?>
                </div>
              <?php endif; ?>
            </td>
            <td style="font-size:13px;color:#495057">
              <?= $t['salon'] ? htmlspecialchars($t['salon']) : '<span style="color:#ADB5BD">—</span>' ?>
            </td>
            <td style="font-size:12.5px;color:#495057;max-width:180px">
              <?= $t['observaciones']
                  ? htmlspecialchars(mb_substr($t['observaciones'],0,60)) . (mb_strlen($t['observaciones'])>60?'…':'')
                  : '<span style="color:#ADB5BD">—</span>' ?>
            </td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="index.php?page=temas&action=edit&id=<?= $t['id_registro'] ?>"
                   class="btn btn-sm" title="Editar">
                  <i class="ti ti-edit"></i>
                </a>
                <button class="btn btn-sm btn-danger" title="Eliminar"
                        onclick="confirmDelete(
                          'index.php?page=temas&action=delete&id=<?= $t['id_registro'] ?>',
                          '<?= addslashes($t['tema']) ?>')">
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