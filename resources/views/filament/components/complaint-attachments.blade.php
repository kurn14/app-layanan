<div style="width: 100%;">
    <div style="display: flex; flex-wrap: wrap; gap: 16px;">
        @foreach ($attachments as $att)
            @php
                $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                $url = asset('storage/' . $att->file_path);
                $fileName = basename($att->file_path);
                $isPhotoType = ($att->type === \App\Enums\ComplaintAttachmentType::PHOTO || $att->type?->value === 'photo' || $att->type === 'photo');
            @endphp

            <div style="width: 280px; max-width: 100%; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.06); display: flex; flex-direction: column;">
                @if ($isImage)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" title="Klik untuk melihat foto ukuran penuh" style="position: relative; display: block; width: 100%; height: 180px; background: #f8fafc; overflow: hidden; text-decoration: none;">
                        <img 
                            src="{{ $url }}" 
                            alt="{{ $fileName }}" 
                            style="width: 100%; height: 180px; object-fit: cover; display: block;"
                            loading="lazy"
                        />
                        <span style="position: absolute; top: 8px; left: 8px; background: rgba(16, 185, 129, 0.95); color: #ffffff; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; pointer-events: none;">
                            Foto Bukti
                        </span>
                        <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(15, 23, 42, 0.75); color: #ffffff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; pointer-events: none;">
                            <svg width="12" height="12" style="width: 12px; height: 12px; min-width: 12px; min-height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Penuh
                        </span>
                    </a>
                @else
                    <div style="width: 100%; height: 180px; background: #f8fafc; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; border-bottom: 1px solid #e2e8f0;">
                        <svg width="40" height="40" style="width: 40px; height: 40px; color: #0d9488;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">{{ $ext ?: 'DOKUMEN' }}</span>
                    </div>
                @endif

                <div style="padding: 10px 12px; display: flex; flex-direction: column; gap: 6px; background: #ffffff;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px; {{ $isPhotoType ? 'background: #ecfdf5; color: #047857;' : 'background: #fffbeb; color: #b45309;' }}">
                            {{ $isPhotoType ? 'Foto Kejadian' : 'Dokumen' }}
                        </span>
                        <span style="font-size: 11px; color: #94a3b8;">
                            {{ $att->created_at?->format('d M Y, H:i') }}
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 6px; margin-top: 2px;">
                        <span style="font-size: 11px; font-family: monospace; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px;" title="{{ $fileName }}">
                            {{ $fileName }}
                        </span>
                        <a 
                            href="{{ $url }}" 
                            target="_blank" 
                            rel="noopener noreferrer" 
                            style="display: inline-flex; align-items: center; gap: 3px; font-size: 12px; font-weight: 600; color: #0d9488; text-decoration: none; cursor: pointer;"
                            onmouseover="this.style.textDecoration='underline'" 
                            onmouseout="this.style.textDecoration='none'"
                        >
                            <span>Buka</span>
                            <svg width="12" height="12" style="width: 12px; height: 12px; min-width: 12px; min-height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
