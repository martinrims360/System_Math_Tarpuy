<?php // views/asistencia/admin.php ?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-calendar-check" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Control de Asistencia</h1>
    <p>Registro diario de asistencia de todos los docentes</p>
  </div>
</div>

<form action="index.php" method="GET" style="display:flex;gap:10px;align-items:end;margin-bottom:18px;flex-wrap:wrap">
  <input type="hidden" name="page" value="asistencia">
  <div>
    <label style="font-size:12.5px;color:#495057;font-weight:500">Fecha</label>
    <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($filtros['fecha']) ?>">
  </div>
  <div>
    <label style="font-size:12.5px;color:#495057;font-weight:500">Docente</label>
    <select name="id_docente" class="form-control">
      <option value="">Todos</option>
      <?php foreach ($docentes as $d): ?>
        <option value="<?= $d['id_docente'] ?>" <?= $filtros['id_docente'] == $d['id_docente'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($d['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label style="font-size:12.5px;color:#495057;font-weight:500">Estado</label>
    <select name="estado" class="form-control">
      <option value="">Todos</option>
      <?php foreach ($estados as $key => $e): ?>
        <option value="<?= $key ?>" <?= $filtros['estado'] === $key ? 'selected' : '' ?>><?= $e['label'] ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="btn btn-primary btn-sm">
    <i class="ti ti-filter"></i> Filtrar
  </button>
  <?php if ($filtros['fecha'] || $filtros['id_docente'] || $filtros['estado']): ?>
    <a href="index.php?page=asistencia" class="btn btn-sm"><i class="ti ti-x"></i> Limpiar</a>
  <?php endif; ?>
</form>

<?php if (empty($registros)): ?>
  <div class="card">
    <div class="empty-state">
      <i class="ti ti-calendar-off"></i>
      <p>No hay registros de asistencia con estos filtros.</p>
    </div>
  </div>

<?php else: ?>
  <div style="margin-bottom:14px;font-size:13px;color:#495057">
    <strong><?= count($registros) ?></strong> registro(s) encontrado(s)
  </div>

  <div class="table-wrap">
    <table class="sys-table">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Docente</th>
          <th>Entrada</th>
          <th>Salida</th>
          <th>Estado</th>
          <th>Registrado por</th>
          <th>Observaciones</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($registros as $r): $e = $estados[$r['estado']]; ?>
          <tr>
            <td><?= date('d/m/Y', strtotime($r['fecha'])) ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div style="width:28px;height:28px;border-radius:50%;background:#EEEDFE;
                            color:#534AB7;display:grid;place-items:center;
                            font-size:11px;font-weight:600;flex-shrink:0">
                  <?= strtoupper(substr($r['docente'], 0, 2)) ?>
                </div>
                <span style="font-weight:500"><?= htmlspecialchars($r['docente']) ?></span>
              </div>
            </td>
            <td><?= $r['hora_entrada'] ? date('H:i', strtotime($r['hora_entrada'])) : '—' ?></td>
            <td><?= $r['hora_salida']  ? date('H:i', strtotime($r['hora_salida']))  : '—' ?></td>
            <td>
              <span class="badge-status" style="background:<?= $e['bg'] ?>;color:<?= $e['text'] ?>">
                <?= $e['label'] ?>
              </span>
            </td>
            <td style="text-transform:capitalize;color:#ADB5BD;font-size:12.5px"><?= htmlspecialchars($r['registrado_por']) ?></td>
            <td style="color:#495057;font-size:12.5px;max-width:200px"><?= htmlspecialchars($r['observaciones'] ?: '—') ?></td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="index.php?page=asistencia&action=edit&id=<?= $r['id_asistencia'] ?>"
                   class="btn btn-sm" title="Editar">
                  <i class="ti ti-edit"></i>
                </a>
                <button class="btn btn-sm btn-danger" title="Eliminar"
                        onclick="confirmDelete(
                          'index.php?page=asistencia&action=delete&id=<?= $r['id_asistencia'] ?>',
                          '<?= addslashes($r['docente'] . ' - ' . date('d/m/Y', strtotime($r['fecha']))) ?>')">
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