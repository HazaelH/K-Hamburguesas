@props(['product'])

<div class="bg-black/40 border border-white/10 rounded-xl overflow-hidden hover:border-orange-500/50 transition duration-300 group">
    <div class="relative h-48 overflow-hidden">
        <img src="{{ $product->imagen_url ?? 'https://placehold.co/400x300' }}" 
             alt="{{ $product->nombre }}" 
             class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
        
        <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded">
            ${{ number_format($product->precio, 2) }}
        </div>
    </div>

    <div class="p-4">
        <h3 class="font-bold text-lg mb-1">{{ $product->nombre }}</h3>
        <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $product->descripcion }}</p>
        
        <button class="w-full bg-white/10 hover:bg-orange-500 text-white py-2 rounded-lg font-bold transition flex items-center justify-center gap-2">
            <i class="fas fa-cart-plus"></i> Agregar
        </button>
    </div>
</div>