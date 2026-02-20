@props([
    'id' => 'confirmModal',
    'type' => 'danger', // danger, warning, info, success
    'title' => '',
    'message' => '',
    'confirmText' => 'Konfirmasi',
    'cancelText' => 'Batal',
    'confirmAction' => '',
    'formMethod' => 'POST',
])

@php
    $iconColors = [
        'danger' => ['bg' => 'bg-red-100 dark:bg-red-900/40', 'text' => 'text-red-600 dark:text-red-400'],
        'warning' => ['bg' => 'bg-amber-100 dark:bg-amber-900/40', 'text' => 'text-amber-600 dark:text-amber-400'],
        'info' => ['bg' => 'bg-blue-100 dark:bg-blue-900/40', 'text' => 'text-blue-600 dark:text-blue-400'],
        'success' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'text' => 'text-emerald-600 dark:text-emerald-400'],
    ];
    
    $buttonColors = [
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'warning' => 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500',
        'info' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500',
    ];
    
    $colors = $iconColors[$type] ?? $iconColors['danger'];
    $buttonColor = $buttonColors[$type] ?? $buttonColors['danger'];
    
    $icons = [
        'danger' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />',
        'warning' => '<path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>',
        'info' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>',
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ];
    
    $icon = $icons[$type] ?? $icons['danger'];
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal('{{ $id }}')"></div>

        {{-- Center modal --}}
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div class="inline-block transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <div class="bg-white dark:bg-gray-800 px-6 pt-8 pb-6">
                {{-- Icon centered --}}
                <div class="flex justify-center mb-4">
                    <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full {{ $colors['bg'] }}">
                        <svg class="h-8 w-8 {{ $colors['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            {!! $icon !!}
                        </svg>
                    </div>
                </div>

                {{-- Title --}}
                @if($title)
                    <h3 class="text-center text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        {{ $title }}
                    </h3>
                @endif

                {{-- Message --}}
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400 whitespace-pre-line">
                        {!! $message !!}
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex items-center justify-center gap-3">
                <button type="button" onclick="closeModal('{{ $id }}')" class="inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition-colors">
                    {{ $cancelText }}
                </button>
                <form id="{{ $id }}-form" method="POST" class="inline">
                    @csrf
                    @if($formMethod === 'DELETE' || $formMethod === 'PUT' || $formMethod === 'PATCH')
                        @method($formMethod)
                    @endif
                    <button type="submit" class="inline-flex items-center rounded-lg {{ $buttonColor }} px-4 py-2 text-sm font-semibold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                        {{ $confirmText }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(modalId, formAction = null) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            if (formAction) {
                const form = document.getElementById(modalId + '-form');
                if (form) {
                    form.action = formAction;
                }
            }
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('[id$="Modal"]');
            modals.forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    closeModal(modal.id);
                }
            });
        }
    });
</script>
