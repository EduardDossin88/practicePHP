<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>База пользователей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">🔍 Поиск по базе данных</h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form id="searchForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Страна</label>
                    <input type="text" class="form-control" name="country" placeholder="Например: Poland">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Пол</label>
                    <select class="form-select" name="gender">
                        <option value="">Все</option>
                        <option value="male">Мужской</option>
                        <option value="female">Женский</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Зарплата от</label>
                    <input type="number" class="form-control" name="min_salary" placeholder="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Зарплата до</label>
                    <input type="number" class="form-control" name="max_salary" placeholder="500000">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">Найти пользователей</button>
                </div>
            </form>
        </div>
    </div>

    <div id="resultsArea" style="display: none;">
        <h5 class="mb-3">Найдено записей: <span id="totalFound" class="badge bg-success">0</span></h5>
        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Страна</th>
                    <th>Город</th>
                    <th>Пол</th>
                    <th>Зарплата</th>
                    <th>Дети</th>
                </tr>
                </thead>
                <tbody id="resultsBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }

        try {
            const response = await fetch(`search.php?${params.toString()}`);
            const result = await response.json();

            if (result.error) {
                alert('Ошибка: ' + result.error);
                return;
            }

            document.getElementById('resultsArea').style.display = 'block';
            document.getElementById('totalFound').textContent = result.total_found;

            const tbody = document.getElementById('resultsBody');
            tbody.innerHTML = '';

            result.data.forEach(user => {
                const row = `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.country}</td>
                        <td>${user.city}</td>
                        <td>${user.gender === 'male' ? 'Муж.' : 'Жен.'}</td>
                        <td><strong>${user.salary}</strong></td>
                        <td>${user.has_children === 'true' ? 'Да' : 'Нет'}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

        } catch (error) {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при связи с сервером.');
        }
    });
</script>

</body>
</html>