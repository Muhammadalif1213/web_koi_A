<x-app-layout>

    {{-- Judul Utama Halaman --}}
    <h1 class="text-3xl font-bold text-white mb-8">Manajemen Pesanan</h1>

    {{-- Container utama Alpine.js untuk mengelola modal --}}
    <div x-data="{
        updateStatusModalOpen: false,
        formAction: '',
        modalTitle: '',
        modalMessage: '',
        newStatus: ''
    }">

        {{-- Judul dan Sub-judul Halaman --}}
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold text-white">Daftar Pesanan Masuk</h1>
        </div>

        {{-- Container Tabel - Dibuat scrollable di layar kecil untuk responsivitas --}}
        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode
                            Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Hp
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->kode_produk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->produk->nama_produk ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->qty }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp{{ number_format($order->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                {{-- Badge Status dengan warna berbeda --}}
                                <span @class([
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    'bg-yellow-100 text-yellow-800' => $order->status === 'menunggu konfirmasi',
                                    'bg-blue-100 text-blue-800' => $order->status === 'diproses',
                                    'bg-green-100 text-green-800' => $order->status === 'selesai',
                                    'bg-red-100 text-red-800' => $order->status === 'dibatalkan',
                                ])>
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $order->alamat ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->no_hp ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{-- Tombol Aksi hanya muncul jika status belum selesai atau dibatalkan --}}
                                @if ($order->status === 'menunggu konfirmasi')
                                    <button type="button"
                                        @click="
                                        updateStatusModalOpen = true;
                                        formAction = '{{ route('admin.orders.updateStatus', $order->id) }}';
                                        modalTitle = 'Konfirmasi Pesanan';
                                        modalMessage = 'Apakah Anda yakin ingin memproses pesanan ini?';
                                        newStatus = 'diproses';
                                    "
                                        class="text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-md text-xs">
                                        Proses
                                    </button>
                                @elseif ($order->status === 'diproses')
                                    <button type="button"
                                        @click="
                                        updateStatusModalOpen = true;
                                        formAction = '{{ route('admin.orders.updateStatus', $order->id) }}';
                                        modalTitle = 'Selesaikan Pesanan';
                                        modalMessage = 'Apakah Anda yakin pesanan ini sudah selesai?';
                                        newStatus = 'selesai';
                                    "
                                        class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded-md text-xs">
                                        Selesaikan
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada pesanan untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginasi --}}
            <div class="p-4 bg-white border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- MODAL KONFIRMASI UPDATE STATUS --}}
        {{-- ========================================================== --}}
        <div x-show="updateStatusModalOpen" x-transition
            class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center"
            x-cloak>
            <div @click.away="updateStatusModalOpen = false"
                class="bg-white rounded-lg shadow-xl text-center p-8 w-full max-w-sm">
                <!-- Ikon Peringatan -->
                <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                    </svg>
                </div>

                <!-- Teks Konfirmasi Dinamis -->
                <h3 class="text-xl font-semibold text-gray-900 mb-2" x-text="modalTitle"></h3>
                <p class="text-gray-600" x-text="modalMessage"></p>

                <!-- Tombol Aksi -->
                <div class="flex justify-center gap-4 mt-6">
                    <button @click="updateStatusModalOpen = false"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-8 rounded-lg transition-colors">
                        Batal
                    </button>
                    <form :action="formAction" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" :value="newStatus">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg transition-colors">
                            Iya
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
