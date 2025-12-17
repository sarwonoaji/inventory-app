@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold">Preview Import Barang</h1>
                <p class="text-sm text-gray-600 mt-1">Periksa dan ubah baris yang diperlukan sebelum menekan Confirm & Import.</p>
            </div>
            <div class="text-sm text-gray-600">
                <div>Valid: <span class="font-medium text-green-600">{{ count($preview) }}</span></div>
                <div>Errors: <span class="font-medium text-red-600">{{ count($errors ?? []) }}</span></div>
            </div>
        </div>

        @php
            $previewErrors = $errors ?? session('import_errors') ?? [];
        @endphp
        @php if(session()->has('import_errors')) { session()->forget('import_errors'); } @endphp
        @if(!empty($previewErrors) && count($previewErrors))
            <div class="mb-4 text-red-700">Beberapa baris memiliki error. Mereka tidak akan diproses.</div>
        @endif

        <form id="confirm-form" action="{{ route('barang.import.confirm') }}" method="POST">
            @csrf

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border-collapse">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left border">#</th>
                            <th class="px-3 py-2 text-left border">Kode</th>
                            <th class="px-3 py-2 text-left border">Nama</th>
                            <th class="px-3 py-2 text-left border">Satuan</th>
                            <th class="px-3 py-2 text-left border">Keterangan</th>
                            <th class="px-3 py-2 text-left border">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @foreach($preview as $i => $row)
                        @php
                            $rowErrors = [];
                            if (!empty($errors)) {
                                foreach($errors as $er) {
                                    if (($er['row'] ?? '') == ($row['row'] ?? '')) { $rowErrors = $er['errors']; break; }
                                }
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border align-top">{{ $row['row'] }}<input type="hidden" name="rows[{{ $i }}][row]" value="{{ $row['row'] }}"></td>
                            <td class="px-3 py-2 border">
                                <input name="rows[{{ $i }}][kode]" value="{{ $row['kode'] }}" class="w-full p-2 border rounded required-input" />
                                <div class="text-xs text-red-600 mt-1 field-error" style="display:none"></div>
                            </td>
                            <td class="px-3 py-2 border">
                                <input name="rows[{{ $i }}][nama]" value="{{ $row['nama'] }}" class="w-full p-2 border rounded required-input" />
                                <div class="text-xs text-red-600 mt-1 field-error" style="display:none"></div>
                            </td>
                            <td class="px-3 py-2 border">
                                <input name="rows[{{ $i }}][satuan]" value="{{ $row['satuan'] }}" class="w-full p-2 border rounded required-input" />
                                <div class="text-xs text-red-600 mt-1 field-error" style="display:none"></div>
                            </td>
                            <td class="px-3 py-2 border"><input name="rows[{{ $i }}][keterangan]" value="{{ $row['keterangan'] }}" class="w-full p-2 border rounded" /></td>
                            <td class="px-3 py-2 border align-top text-sm">
                                @if(!empty($rowErrors))
                                    <div class="text-red-600 font-medium">Error</div>
                                    <ul class="text-xs text-red-600 mt-1">
                                        @foreach($rowErrors as $re)
                                            <li>- {{ $re }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-green-600 font-medium">OK</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-600">Periksa kembali data sebelum konfirmasi. Baris dengan status Error tidak akan diproses jika tidak diperbaiki.</div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('barang.import.form') }}" class="px-4 py-2 border rounded text-gray-700">Back</a>
                    <button id="confirm-btn" type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Confirm & Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('confirm-form');
    if (!form) return;

    const validate = () => {
        let valid = true;
        // reset
        form.querySelectorAll('.required-input').forEach(inp => {
            inp.classList.remove('border-red-600');
            const err = inp.closest('td')?.querySelector('.field-error');
            if (err) { err.style.display = 'none'; err.textContent = ''; }
        });

        form.querySelectorAll('.required-input').forEach(inp => {
            if (!inp.value || inp.value.trim() === '') {
                valid = false;
                inp.classList.add('border-red-600');
                const err = inp.closest('td')?.querySelector('.field-error');
                if (err) { err.style.display = 'block'; err.textContent = 'Field wajib diisi'; }
            }
        });

        return valid;
    };

    form.addEventListener('submit', function(e){
        if (!validate()) {
            e.preventDefault();
            window.scrollTo({ top: form.getBoundingClientRect().top + window.scrollY - 100, behavior: 'smooth' });
        }
    });

    // live remove error when typing
    form.addEventListener('input', function(e){
        const t = e.target;
        if (t.classList && t.classList.contains('required-input')) {
            if (t.value && t.value.trim() !== '') {
                t.classList.remove('border-red-600');
                const err = t.closest('td')?.querySelector('.field-error');
                if (err) { err.style.display = 'none'; err.textContent = ''; }
            }
        }
    });
});
</script>

@endsection

<script>
// Autosave edited preview rows to session via AJAX (debounced)
document.addEventListener('DOMContentLoaded', function(){
    const saveUrl = '{{ route('barang.import.preview.save') }}';
    const form = document.getElementById('confirm-form');
    if (!form) return;

    let tmr;
    const gatherRows = () => {
        const rows = [];
        form.querySelectorAll('tbody tr').forEach((tr, idx) => {
            const row = {};
            tr.querySelectorAll('input').forEach(inp => {
                const name = inp.getAttribute('name');
                if (!name) return;
                const m = name.match(/rows\[(\d+)\]\[(.+)\]/);
                if (m) {
                    const key = m[2];
                    row[key] = inp.value;
                }
            });
            rows.push(row);
        });
        return rows;
    };

    const save = () => {
        const rows = gatherRows();
        fetch(saveUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ rows })
        }).catch(()=>{});
    };

    form.addEventListener('input', function(){
        clearTimeout(tmr);
        tmr = setTimeout(save, 600);
    });
});
</script>
