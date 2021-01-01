<!DOCTYPE html>
<html>
<head>
    <title>Employees</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" integrity="sha256-+N4/V/SbAFiW1MPBCXnfnP9QSN3+Keu+NlB+0ev/YKQ=" crossorigin="anonymous" />
</head>
<body>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <table class="table" id="employees-table">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Age</th>
                <th scope="col">Kids number</th>
                <th scope="col">Company car</th>
                <th scope="col">Source salary</th>
                <th scope="col">Calculated salary</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div class="text-center"><button type="submit" class="btn btn-primary btn-add"><i class="fas fa-plus"></i> Add an employee</button></div>

    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <form id="edit-form" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-name">Name</label>
                        <input required type="text" id="edit-name" class="form-control" placeholder="Enter name">
                    </div>
                    <div class="form-group">
                        <label for="edit-birthdate">Birth date</label>
                        <input required type="date" id="edit-birthdate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="edit-kids">Kids number</label>
                        <input required min="0" step="1" type="number" id="edit-kids" class="form-control" placeholder="Enter kids number">
                    </div>
                    <div class="form-group">
                        <label for="edit-salary">Salary</label>
                        <input required min="0" step="0.01" type="number" id="edit-salary" class="form-control" placeholder="Enter salary">
                    </div>
                    <div class="form-group form-check">
                        <input name="company_car" type="checkbox" class="form-check-input" id="edit-car">
                        <label class="form-check-label" for="edit-car">Company car</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        let App = {
            model: null,
            init: function() {
                $.post('/api/employee/list', function(data){
                    if (!data.error) {
                        App.model = data.data;
                        App.drawTable();
                        $(".btn-add").click(function(){
                            App.showAddModal();
                        });
                    }
                });
            },
            addRow: function(data) {
                let row = $("<tr>");
                let car = data.company_car ? "Yes" : "No";
                row.addClass("employee-row");
                row.attr("data-id", data.employee_id);
                row.append("<td>" + data.name + "</td>");
                row.append("<td>" + data.age + "</td>");
                row.append("<td>" + data.kids_num + "</td>");
                row.append("<td>" + car + "</td>");
                row.append("<td>" + data.salary + "</td>");
                row.append("<td>" + data.calculated_salary + "</td>");
                let buttons = $("<td>");
                let edit_button = $('<button type="button" class="btn btn-warning btn-edit"><i class="fas fa-pencil-alt"></i></button>');
                let remove_button = $('<button type="button" class="btn btn-danger btn-remove"><i class="fas fa-trash-alt"></i></button>');
                edit_button.click(function(){
                    App.showEditModal(data.employee_id);
                });
                remove_button.click(function(){
                    App.removeItem(data.employee_id);
                });
                buttons.append(edit_button);
                buttons.append(remove_button);
                row.append(buttons);
                $("#employees-table tbody").append(row);
            },
            drawTable: function() {
                for (let i in this.model) {
                    this.addRow(this.model[i]);
                }
            },
            redrawTable: function() {
                $("#employees-table tbody").html("");
                this.drawTable();
            },
            showEditModal: function(id) {
                $("#edit-name").val(id ? this.model[id].name : '');
                $("#edit-birthdate").val(id ? this.model[id].birth_date : '');
                $("#edit-kids").val(id ? this.model[id].kids_num : '');
                $("#edit-salary").val(id ? this.model[id].salary : '');
                $('#edit-car').prop('checked', id ? this.model[id].company_car : false);
                $("#edit-form").unbind("submit").submit(function(e){
                    e.preventDefault();
                    let data = {
                        name: $("#edit-name").val(),
                        birth_date: $("#edit-birthdate").val(),
                        kids_num: $("#edit-kids").val(),
                        salary: $("#edit-salary").val(),
                        company_car: $("#edit-car").prop('checked')
                    };
                    if (id) {
                        data.employee_id = id;
                        App.editItem(data);
                    } else {
                        App.addItem(data);
                    }
                });
                $("#edit-modal").modal("show");
            },
            showAddModal: function() {
                this.showEditModal(0);
            },
            addItem(data) {
                $.post('/api/employee/add', JSON.stringify(data), function(data){
                    if (!data.error) {
                        App.model[data.data.employee_id] = data.data;
                        App.addRow(data.data);
                        $("#edit-modal").modal("hide");
                    }
                });
            },
            editItem(data) {
                $.post('/api/employee/edit', JSON.stringify(data), function(data){
                    if (!data.error) {
                        App.model[data.data.employee_id] = data.data;
                        App.redrawTable();
                        $("#edit-modal").modal("hide");
                    }
                });
            },
            removeItem(id) {
                let data = JSON.stringify({
                    employee_id: id
                });
                $.post('/api/employee/remove', data, function(data){
                    if (!data.error) {
                        delete App.model[id];
                        $(".employee-row[data-id='" + id + "']").remove();
                    }
                });
            }
        };

        $(document).ready(function() {
            App.init();
        });
    </script>
</body>
</html>