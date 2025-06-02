<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$username = htmlspecialchars($_SESSION['user']['username']);

// CRUD operations
if (isset($_POST['add_book']) || isset($_POST['update_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];
    $quantity = $_POST['quantity'];
    if (isset($_POST['add_book'])) {
        $stmt = $conn->prepare('INSERT INTO books (title,author,year,genre,quantity) VALUES (?,?,?,?,?)');
        $stmt->bind_param('ssisi', $title, $author, $year, $genre, $quantity);
    } else {
        $stmt = $conn->prepare('UPDATE books SET title=?,author=?,year=?,genre=?,quantity=? WHERE id=?');
        $stmt->bind_param('ssisii', $title, $author, $year, $genre, $quantity, $_POST['id']);
    }
    $stmt->execute();
    header('Location: libros.php');
    exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM books WHERE id=$id");
    header('Location: libros.php');
    exit;
}
// Loan/Return
if (isset($_POST['loan_book'])) {
    $book_id = (int)$_POST['loan_book'];
    $reader_id = (int)$_POST['reader_id'];
    $conn->query("UPDATE books SET quantity = quantity - 1 WHERE id=$book_id");
    $stmt = $conn->prepare('INSERT INTO transactions (user_id,book_id,date_of_issue) VALUES (?,?,CURDATE())');
    $stmt->bind_param('ii', $reader_id, $book_id);
    $stmt->execute();
    header('Location: libros.php');
    exit;
}
if (isset($_POST['return_book'])) {
    $trans_id = (int)$_POST['return_book'];
    $conn->query("UPDATE transactions SET date_of_return = CURDATE() WHERE id=$trans_id");
    $res = $conn->query("SELECT book_id FROM transactions WHERE id=$trans_id");
    $row = $res->fetch_assoc();
    $conn->query("UPDATE books SET quantity = quantity + 1 WHERE id={$row['book_id']}");
    header('Location: libros.php');
    exit;
}
// Data fetch
$books = $conn->query('SELECT * FROM books')->fetch_all(MYSQLI_ASSOC);
$readers = $conn->query('SELECT id,username FROM users WHERE role_id=3')->fetch_all(MYSQLI_ASSOC);
$activeLoans = [];
$res = $conn->query('SELECT t.id,t.book_id,u.username FROM transactions t JOIN users u ON t.user_id=u.id WHERE t.date_of_return IS NULL');
while ($r = $res->fetch_assoc()) {
    $activeLoans[$r['book_id']][] = ['id' => $r['id'], 'username' => $r['username']];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <style>
    body { background:rgb(247, 247, 248); font-family: 'Inter', sans-serif; }
    .header { display: flex; justify-content: space-between; align-items: center; margin: 2rem 0; }
    .table tbody tr { animation: fadeInUp 0.6s both; }
    @keyframes fadeInUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
  </style>
</head>
<body class="container">
  <div class="header">
    <h3 class="text-primary animate__animated animate__bounceIn"><i class="bi bi-journal-bookmark-fill"></i> Hola, <?= $username ?></h3>
    <div>
      <button class="btn btn-success animate__animated animate__pulse me-2" data-bs-toggle="modal" data-bs-target="#bookModal"><i class="bi bi-plus-lg"></i> Agregar</button>
      <a href="../dashboard.php" class="btn btn-outline-danger animate__animated animate__pulse"><i class="bi bi-box-arrow-right"></i> Regresar</a>
    </div>
  </div>
  <div class="table-responsive animate__animated animate__fadeInUp">
    <table class="table table-hover align-middle">
      <thead class="table-dark"><tr>#</tr><tr><th>#</th><th>Título</th><th>Autor</th><th>Año</th><th>Género</th><th>Cantidad</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($books as $i => $b): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($b['title']) ?></td>
          <td><?= htmlspecialchars($b['author']) ?></td>
          <td><?= $b['year'] ?></td>
          <td><?= htmlspecialchars($b['genre']) ?></td>
          <td class="text-center"><?= $b['quantity'] ?></td>
          <td>
            <!-- Edit -->
            <button class="btn btn-sm btn-outline-warning me-1 animate__animated animate__pulse btnEdit" data-id="<?= $b['id'] ?>" data-title="<?= htmlspecialchars($b['title']) ?>" data-author="<?= htmlspecialchars($b['author']) ?>" data-year="<?= $b['year'] ?>" data-genre="<?= htmlspecialchars($b['genre']) ?>" data-quantity="<?= $b['quantity'] ?>" data-bs-toggle="modal" data-bs-target="#bookModal"><i class="bi bi-pencil-fill"></i></button>
            <!-- Delete -->
            <a href="?delete=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger me-1 animate__animated animate__pulse" onclick="return confirm('¿Eliminar?');"><i class="bi bi-trash-fill"></i></a>
            <!-- Loan -->
            <button class="btn btn-sm btn-primary me-1 animate__animated animate__pulse" data-bs-toggle="modal" data-bs-target="#loanModal" data-id="<?= $b['id'] ?>" data-title="<?= htmlspecialchars($b['title']) ?>" <?= $b['quantity']==0?'disabled':'' ?>><i class="bi bi-box-arrow-in-right"></i></button>
            <!-- Return -->
            <button class="btn btn-sm btn-info animate__animated animate__pulse ms-1" data-bs-toggle="modal" data-bs-target="#returnModal" data-id="<?= $b['id'] ?>"><i class="bi bi-arrow-counterclockwise"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Book Modal -->
  <div class="modal fade" id="bookModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered animate__animated animate__zoomIn">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="bookModalLabel"><i class="bi bi-book-fill"></i> Nuevo Libro</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="libros.php">
          <div class="modal-body">
            <input type="hidden" name="id" id="bookId">
            <div class="mb-3"><label class="form-label">Título</label><input type="text" name="title" id="bookTitle" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Autor</label><input type="text" name="author" id="bookAuthor" class="form-control" required></div>
            <div class="row"><div class="col mb-3"><label class="form-label">Año</label><input type="number" name="year" id="bookYear" class="form-control" required></div><div class="col mb-3"><label class="form-label">Cantidad</label><input type="number" name="quantity" id="bookQuantity" class="form-control" required></div></div>
            <div class="mb-3"><label class="form-label">Género</label><input type="text" name="genre" id="bookGenre" class="form-control" required></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="add_book" class="btn btn-primary" id="bookSubmitBtn"><i class="bi bi-save"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Loan Modal -->
  <div class="modal fade" id="loanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered animate__animated animate__zoomIn">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-box-arrow-in-right"></i> Prestar Libro</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="libros.php">
          <div class="modal-body">
            <input type="hidden" name="loan_book" id="loanBookId">
            <div class="mb-3"><label class="form-label">Libro</label><input type="text" id="loanBookTitle" class="form-control" readonly></div>
            <div class="mb-3"><label class="form-label">Lector</label><select name="reader_id" class="form-select" required><option value="">-- Elige lector --</option><?php foreach($readers as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option><?php endforeach; ?></select></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Prestar</button></div>
        </form>
      </div>
    </div>
  </div>

  <!-- Return Modal -->
  <div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered animate__animated animate__zoomIn">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="bi bi-arrow-counterclockwise"></i> Devolver Libro</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="libros.php">
          <div class="modal-body">
            <input type="hidden" name="return_book" id="returnBookId">
            <div class="mb-3"><label class="form-label">Préstamos Activos</label><select id="transSelect" name="return_book" class="form-select" required></select></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Devolver</button></div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const activeLoans = <?= json_encode($activeLoans) ?>;
    // Book modal for edit
    document.querySelectorAll('.btnEdit').forEach(btn => btn.addEventListener('click', () => {
      document.getElementById('bookModalLabel').innerHTML = '<i class="bi bi-pencil-fill"></i> Editar Libro';
      document.getElementById('bookSubmitBtn').name = 'update_book';
      document.getElementById('bookSubmitBtn').innerHTML = '<i class="bi bi-pencil-fill"></i> Actualizar';
      ['bookId','bookTitle','bookAuthor','bookYear','bookQuantity','bookGenre'].forEach(id => document.getElementById(id).value = btn.dataset[id.replace('book','').toLowerCase()]);
    }));
    document.getElementById('bookModal').addEventListener('show.bs.modal', e => {
      if (!e.relatedTarget.classList.contains('btnEdit')) {
        document.getElementById('bookModalLabel').innerHTML = '<i class="bi bi-book-fill"></i> Nuevo Libro';
        document.getElementById('bookSubmitBtn').name = 'add_book';
        document.getElementById('bookSubmitBtn').innerHTML = '<i class="bi bi-save"></i> Guardar';
        ['bookId','bookTitle','bookAuthor','bookYear','bookQuantity','bookGenre'].forEach(id => document.getElementById(id).value = '');
      }
    });
    // Loan modal
    document.getElementById('loanModal').addEventListener('show.bs.modal', e => {
      const btn = e.relatedTarget;
      document.getElementById('loanBookId').value = btn.dataset.id;
      document.getElementById('loanBookTitle').value = btn.dataset.title;
    });
    // Return modal
    document.getElementById('returnModal').addEventListener('show.bs.modal', e => {
      const bookId = e.relatedTarget.dataset.id;
      const sel = document.getElementById('transSelect'); sel.innerHTML = '';
      (activeLoans[bookId] || []).forEach(tr => {
        const opt = document.createElement('option'); opt.value = tr.id; opt.textContent = tr.username; sel.append(opt);
      });
    });
  </script>
</body>
</html>
