document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('serviceForm');
    if (!form) return;

    var nameIn = document.getElementById('customerName');
    var phoneIn = document.getElementById('phone');
    var emailIn = document.getElementById('email');
    var regIn = document.getElementById('regNumber');
    var typeSel = document.getElementById('vehicleType');
    var modelIn = document.getElementById('vehicleModel');
    var yearIn = document.getElementById('yearOfManufacture');
    var dateIn = document.getElementById('serviceDate');
    var checks = document.querySelectorAll('input[name="services[]"]');
    var countEl = document.getElementById('svcCount');
    var listEl = document.getElementById('svcList');
    var totalEl = document.getElementById('grandTotal');
    var costIn = document.getElementById('estimatedCost');
    var typeHint = document.getElementById('typeHint');
    var ageHint = document.getElementById('ageHint');
    var resetBtn = document.getElementById('resetBtn');

    var today = new Date().toISOString().split('T')[0];
    dateIn.setAttribute('min', today);
    var currentYear = new Date().getFullYear();

    var typeMsgs = {
        'Saloon Car': 'Saloon cars usually need regular oil changes and alignment.',
        'SUV': 'SUVs benefit from full service and brake checks due to higher load.',
        'Pickup': 'Pickups often need tyre balancing and brake inspection.',
        'Van': 'Vans used for work benefit from computer diagnosis and oil changes.'
    };

    function calculateServiceCost() {
        var total = 0;
        var count = 0;
        var items = [];

        checks.forEach(function (cb) {
            if (cb.checked) {
                var p = parseInt(cb.getAttribute('data-price'), 10);
                total += p;
                count++;
                items.push({ name: cb.value, price: p });
            }
        });

        countEl.textContent = count;

        if (items.length === 0) {
            listEl.innerHTML = '<li class="empty">No services yet</li>';
        } else {
            var html = '';
            items.forEach(function (it) {
                html += '<li><span>' + it.name + '</span><span>KSh ' + it.price.toLocaleString() + '</span></li>';
            });
            listEl.innerHTML = html;
        }

        totalEl.textContent = 'KSh ' + total.toLocaleString();
        costIn.value = total;
        return total;
    }

    function checkVehicleAge() {
        var y = parseInt(yearIn.value, 10);
        if (!y || isNaN(y)) {
            ageHint.hidden = true;
            return;
        }
        var age = currentYear - y;
        if (age > 15) {
            ageHint.hidden = false;
            ageHint.className = 'hint-box warn';
            ageHint.textContent = 'This vehicle is ' + age + ' years old. Consider Computer Diagnosis and Brake Inspection.';
        } else if (age >= 0) {
            ageHint.hidden = false;
            ageHint.className = 'hint-box';
            ageHint.textContent = 'Vehicle age: ' + age + ' year(s). Within normal range.';
        } else {
            ageHint.hidden = true;
        }
    }

    function showTypeInfo() {
        var t = typeSel.value;
        if (t && typeMsgs[t]) {
            typeHint.hidden = false;
            typeHint.textContent = typeMsgs[t];
        } else {
            typeHint.hidden = true;
        }
    }

    function showError(id, errId, msg) {
        var el = document.getElementById(id);
        var er = document.getElementById(errId);
        if (el) el.classList.add('error');
        if (er) er.textContent = msg;
    }

    function clearErrors() {
        document.querySelectorAll('.err').forEach(function (e) {
            e.textContent = '';
        });
        document.querySelectorAll('.error').forEach(function (e) {
            e.classList.remove('error');
        });
    }

    function validateServiceForm() {
        clearErrors();
        var ok = true;

        if (nameIn.value.trim().length < 3) {
            showError('customerName', 'nameError', 'Full name is required.');
            ok = false;
        }

        var ph = phoneIn.value.trim();
        if (ph === '' || !/^\d{10}$/.test(ph)) {
            showError('phone', 'phoneError', 'Phone must be exactly 10 digits.');
            ok = false;
        }

        var em = emailIn.value.trim();
        if (em === '' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) {
            showError('email', 'emailError', 'Enter a valid email.');
            ok = false;
        }

        if (regIn.value.trim() === '') {
            showError('regNumber', 'regError', 'Registration number is required.');
            ok = false;
        }

        if (typeSel.value === '') {
            showError('vehicleType', 'typeError', 'Select vehicle type.');
            ok = false;
        }

        if (modelIn.value.trim() === '') {
            showError('vehicleModel', 'modelError', 'Vehicle model is required.');
            ok = false;
        }

        var yr = parseInt(yearIn.value, 10);
        if (!yearIn.value || isNaN(yr) || yr < 1980 || yr > currentYear) {
            showError('yearOfManufacture', 'yearError', 'Year must be between 1980 and ' + currentYear + '.');
            ok = false;
        }

        if (!dateIn.value) {
            showError('serviceDate', 'dateError', 'Preferred date is required.');
            ok = false;
        } else if (dateIn.value < today) {
            showError('serviceDate', 'dateError', 'Date cannot be in the past.');
            ok = false;
        }

        var any = false;
        checks.forEach(function (c) {
            if (c.checked) any = true;
        });
        if (!any) {
            document.getElementById('servicesError').textContent = 'Select at least one service.';
            ok = false;
        }

        return ok;
    }

    checks.forEach(function (c) {
        c.addEventListener('change', calculateServiceCost);
    });

    typeSel.addEventListener('change', function () {
        showTypeInfo();
    });

    yearIn.addEventListener('input', checkVehicleAge);

    form.addEventListener('submit', function (e) {
        if (!validateServiceForm()) {
            e.preventDefault();
            var fe = document.querySelector('.error, .err:not(:empty)');
            if (fe) fe.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    resetBtn.addEventListener('click', function () {
        setTimeout(function () {
            calculateServiceCost();
            showTypeInfo();
            checkVehicleAge();
            clearErrors();
        }, 10);
    });

    calculateServiceCost();
});
