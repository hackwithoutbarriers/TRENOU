@php
    $whatsappNumber = config('services.whatsapp.number', '22890585976');
    $whatsappMessage = urlencode('Bonjour, je souhaite en savoir plus.');
    $whatsappHref = 'https://wa.me/' . $whatsappNumber . '?text=' . $whatsappMessage;
@endphp

<a
    href="{{ $whatsappHref }}"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Contacter sur WhatsApp"
    class="group fixed bottom-5 right-5 z-50 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#25D366] shadow-[0_10px_30px_rgba(37,211,102,0.35)] transition duration-200 hover:scale-105"
>
    <span class="absolute inset-0 rounded-full bg-[#25D366]/30 animate-ping opacity-60"></span>
    <svg class="relative h-7 w-7 text-white" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
        <path d="M18.9 5.6A10.3 10.3 0 0 0 12.2 2C6.7 2 2.2 6.5 2.2 12.1c0 1.8.5 3.6 1.3 5.1L2 22l5-1.4c1.5.9 3.2 1.3 5 1.3h.1c5.5 0 10-4.5 10-10.1 0-2.7-1.1-5.2-3.1-7.1Zm-6.7 15.3c-1.4 0-2.8-.4-4-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8.3 8.3 0 0 1 3.8 12c0-4.6 3.8-8.3 8.5-8.3 2.3 0 4.4.9 6 2.6A8.2 8.2 0 0 1 20.4 12c-.1 4.5-3.8 8.3-8.2 8.3Zm4.6-6.2c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8 1-.2.2-.3.2-.6.1-1.7-.9-2.7-1.6-3.8-3.4-.3-.5.3-.5.6-.6l.3-.1c.1-.1.2-.2.3-.4.1-.2 0-.4 0-.5l-.1-.4c-.1-.3-.6-1.5-.8-2.1-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9S8 9.5 8.1 9.7c.2.2 2.4 3.8 5.9 5.3.8.3 1.4.5 1.9.6.8.3 1.5.2 2.1.1.7-.1 1.9-.8 2.2-1.6.3-.8.3-1.5.2-1.6-.1-.1-.3-.2-.5-.3Z"/>
    </svg>
</a>
