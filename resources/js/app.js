import $ from 'jquery';
import Swal from 'sweetalert2';
import { initOrderForm } from './order-form';

window.$ = window.jQuery = $;

$(function () {
    // Mobile navigation
    $('#nav-toggle').on('click', function () {
        $('#sidebar').toggleClass('-translate-x-full');
        $('#nav-overlay').toggleClass('hidden');
    });
    $('#nav-overlay').on('click', function () {
        $('#sidebar').addClass('-translate-x-full');
        $(this).addClass('hidden');
    });

    // Auto-dismiss flash messages
    $('[data-flash]').delay(6000).fadeOut(400);

    // Confirm destructive / irreversible actions
    $('form[data-confirm]').on('submit', function (e) {
        e.preventDefault();

        const form    = this;
        const message = $(form).data('confirm');
        const isComplete = $(form).find('button').hasClass('btn-success');

        Swal.fire({
            title: isComplete ? 'Complete order?' : 'Cancel order?',
            text: message,
            icon: isComplete ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonText: isComplete ? 'Yes, complete it' : 'Yes, cancel it',
            cancelButtonText: 'Go back',
            confirmButtonColor: isComplete ? '#16a34a' : '#dc2626',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });

    $('[data-print]').on('click', function () {
        window.print();
    });

    initOrderForm();
});
