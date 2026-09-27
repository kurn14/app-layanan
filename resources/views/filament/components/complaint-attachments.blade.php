<div class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($attachments as $att)
            @php
                $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                $url = asset('storage/' . $att->file_path);
                $fileName = basename($att->file_path);
                $isPhotoType = ($att->type === \App\Enums\ComplaintAttachmentType::PHOTO || $att->type?->value === 'photo' || $att->type === 'photo');
            @endphp

            <div class="flex flex-col justify-between rounded-xl border border-gray-200 bg-white p-3 shadow-xs dark:border-gray-700 dark:bg-gray-800 transition hover:shadow-md">
                @if ($isImage)
                    <div class="relative group aspect-video w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-900">
                        <img 
                            src="{{ $url }}" 
                            alt="{{ $fileName }}" 
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            loading="lazy"
                        />
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <a 
                                href="{{ $url }}" 
                                target="_blank" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/90 text-xs font-semibold text-gray-800 hover:bg-white shadow"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Penuh</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="aspect-video w-full flex flex-col items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/50 text-gray-500">
                        <svg class="w-12 h-12 text-primary-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-xs font-semibold uppercase text-gray-400">{{ $ext ?: 'DOKUMEN' }}</span>
                    </div>
                @endif

                <div class="mt-3 flex flex-col gap-1.5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium {{ $isPhotoType ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' }}">
                            {{ $isPhotoType ? 'Foto Bukti' : 'Dokumen Pendukung' }}
                        </span>
                        <span class="text-[11px] text-gray-400">
                            {{ $att->created_at?->format('d M Y H:i') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-gray-100 dark:border-gray-700/60 text-xs">
                        <span class="text-gray-600 dark:text-gray-300 truncate max-w-[180px] font-mono" title="{{ $fileName }}">
                            {{ $fileName }}
                        </span>
                        <a 
                            href="{{ $url }}" 
                            target="_blank" 
                            class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 font-semibold hover:underline"
                        >
                            <span>Buka</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
