const input = document.querySelector("#phone");

window.intlTelInput(input, {        
        separateDialCode: true,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@25.1.0/build/js/utils.js",
         
    });

 document.getElementById('transcript_file').addEventListener('change', function() {
    const fileName = this.files.length > 0 ? this.files[0].name : "No file selected.";
    document.getElementById('file_name_display').textContent = fileName;
});


document.addEventListener("click", function (e) {
    if (e.target.classList.contains("edit-btn")) {
        const record = JSON.parse(e.target.dataset.record);

        // Fill modal fields
        document.getElementById("edit-id").value = record.id;
        document.getElementById("edit-name").value = record.name;
        document.getElementById("edit-email").value = record.email;
        document.getElementById("edit-address").value = record.address;
        document.getElementById("edit-phone").value = record.phone;
        document.getElementById("edit-level").value = record.level;

        // Handle gender radio separately
        document.querySelectorAll("input[name='edit-gender']").forEach(radio => {
            radio.checked = (radio.value === record.gender);
        });
    }
});

document.getElementById("ajax-update").addEventListener("click", function (e) {
    e.preventDefault();

    let id = document.getElementById("edit-id").value;
    let name = document.getElementById("edit-name").value;
    let email = document.getElementById("edit-email").value;
    let address = document.getElementById("edit-address").value;
    let phone = document.getElementById("edit-phone").value;
    let gender = document.querySelector("input[name='edit-gender']:checked")?.value || "";

    let level = document.getElementById("edit-level").value;

    fetch("ajax-handler.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            action: "update",
            id, name, email, address, phone, gender, level
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            const row = document.querySelector(`#row-${id}`);
            if (row) {
                row.children[1].textContent = name;
                row.children[2].textContent = email;
                row.children[3].textContent = address;
                row.children[4].textContent = phone;
                row.children[5].textContent = gender;
                row.children[6].textContent = level;
            }
            document.querySelector("#editModal .btn-close").click();
        } else {
            alert("Update failed: " + data.message);
        }
    })
    .catch(err => console.error("Error:", err));
});

document.querySelectorAll(".open-delete-modal").forEach(btn => {
    btn.addEventListener("click", function() {
        document.getElementById("delete-id-hidden").value = this.dataset.id;
    });
});

document.querySelector("#deleteConfirmModal form").addEventListener("submit", function (e) {
    e.preventDefault();
    let id = document.getElementById("delete-id-hidden").value;

    fetch("ajax-handler.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ action: "delete", id })
    })
    .then(res => res.json())
    .then(data => {
    if (data.status === "success") {
        
        
        const row = document.querySelector(`#row-${id}`);
        if (row) row.remove();

        // Add dynamically to deleted records table
        const deletedTableBody = document.querySelector(".table-danger + tbody, .table.table-bordered:nth-of-type(2) tbody");
        if (deletedTableBody) {
            const d = data.data;
            const newRow = document.createElement("tr");

            newRow.innerHTML = `
                <td>${d.id}</td>
                <td>${d.name}</td>
                <td>${d.email}</td>
                <td>${d.address}</td>
                <td>${d.phone}</td>
                <td>${d.gender}</td>
                <td>${d.level}</td>
                
                <td>
                    <button 
                        class="btn btn-success btn-sm open-restore-modal"
                        data-id="${d.id}"
                        data-bs-toggle="modal"
                        data-bs-target="#restoreConfirmModal">
                        Restore
                    </button>
                </td>
            `;

            deletedTableBody.appendChild(newRow);

            // Re-attach restore modal listener to new button
            newRow.querySelector(".open-restore-modal").addEventListener("click", function() {
                document.getElementById("restore-id-hidden").value = this.dataset.id;
            });
        }

        // Close modal
    document.querySelector("#deleteConfirmModal .btn-close").click();
    } else {
        alert("Delete failed: " + data.message);
    }
})

    .catch(err => console.error("Error:", err));
});


document.querySelectorAll(".open-restore-modal").forEach(btn => {
    btn.addEventListener("click", function() {
        document.getElementById("restore-id-hidden").value = this.dataset.id;
    });
});


document.querySelector("#restoreConfirmModal form").addEventListener("submit", function (e) {
    e.preventDefault();
    let id = document.getElementById("restore-id-hidden").value;

    fetch("ajax-handler.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ action: "restore", id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            

            const d = data.data;

            // Remove from deleted table
            const deletedRow = document.querySelector(`.open-restore-modal[data-id="${id}"]`)?.closest("tr");
            if (deletedRow) deletedRow.remove();

            // Add back to main table dynamically
            const mainTableBody = document.querySelector("table.table-bordered tbody");
            const newRow = document.createElement("tr");
            newRow.id = `row-${d.id}`;
            newRow.innerHTML = `
                <td>${d.id}</td>
                <td>${d.name}</td>
                <td>${d.email}</td>
                <td>${d.address}</td>
                <td>${d.phone}</td>
                <td>${d.gender}</td>
                <td>${d.level}</td>
                
                <td>
                    <button 
                        class="btn btn-primary btn-sm edit-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-record='${JSON.stringify(d)}'>Edit</button>

                    <button 
                        class="btn btn-danger btn-sm open-delete-modal"
                        data-id="${d.id}"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal">Delete</button>
                </td>
            `;

            mainTableBody.appendChild(newRow);

            // Re-attach button listeners for edit & delete
            newRow.querySelector(".edit-btn").addEventListener("click", function () {
                let data = JSON.parse(this.dataset.record);
                document.getElementById("edit-id").value = data.id;
                document.getElementById("edit-name").value = data.name;
                document.getElementById("edit-email").value = data.email;
                document.getElementById("edit-address").value = data.age;
                document.getElementById("edit-phone").value = data.phone;
                document.querySelectorAll("input[name='gender']").forEach(radio => {
                    radio.checked = (radio.value === data.gender);
                });
                document.getElementById("edit-level").value = data.level;
                
            });

            newRow.querySelector(".open-delete-modal").addEventListener("click", function() {
                document.getElementById("delete-id-hidden").value = this.dataset.id;
            });

            // Close restore modal
            document.querySelector("#restoreConfirmModal .btn-close").click();

            // Highlight restored row
            newRow.style.backgroundColor = "#cff4fc";
            setTimeout(() => newRow.style.backgroundColor = "", 1000);

        } else {
            alert("Restore failed: " + data.message);
        }
    })
    .catch(err => console.error("Error:", err));
});



document.querySelector("#insert-form").addEventListener("submit", function (e) {
    e.preventDefault();
    const form = this;
    const name = form.querySelector("#name").value.trim();
    const email = form.querySelector("#email").value.trim();
    const address = form.querySelector("#address").value.trim();
    const phone = form.querySelector("#phone").value.trim();
    const gender = form.querySelector("input[name='gender']:checked")?.value || "";
    const level = form.querySelector("#dropdown").value;

    if (!name || !email || !address || !phone || !gender || !level) {
        alert("Please fill all required fields.");
        return;
    }

    fetch("ajax-handler.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ action: "insert", name, email, address, phone, gender, level })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            const d = data.data;
            const tableBody = document.querySelector("table.table tbody");
            const newRow = document.createElement("tr");
            newRow.id = `row-${d.id}`;
            newRow.innerHTML = `
                <td>${d.id}</td>
                <td>${d.name}</td>
                <td>${d.email}</td>
                <td>${d.address}</td>
                <td>${d.phone}</td>
                <td>${d.gender}</td>
                <td>${d.level}</td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-record='${JSON.stringify(d)}'>Edit</button>
                    <button class="btn btn-danger btn-sm open-delete-modal" data-id="${d.id}" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">Delete</button>
                </td>`;
            tableBody.appendChild(newRow);
            form.reset();
        } else {
            alert("Insert failed: " + data.message);
        }
    });
});
