<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { ArrowLeft, Share2, Heart } from 'lucide-vue-next'

const props = defineProps<{
  atelier: {
    id: number
    slug: string
    name: string
    logo_url?: string
  }
  product: {
    id: number
    slug: string
    name: string
    short_desc?: string
    long_desc?: string
    price: number
    currency: string
    is_personalizable: boolean
    category?: {
      id: number
      name: string
      slug: string
    }
    photos: Array<{
      id: number
      url: string
      thumb: string
      is_cover: boolean
    }>
  }
}>()

const selectedImageIndex = ref(0)

const fmtMoney = (value: number, currency = 'BRL', locale = 'pt-BR') =>
  new Intl.NumberFormat(locale, { style: 'currency', currency }).format(value ?? 0)

const mainImage = computed(() => props.product.photos[selectedImageIndex.value]?.url || '/placeholder.jpg')

const goBack = () => window.history.back()

const contactWhatsApp = () => {
  const message = `Olá! Tenho interesse no produto: ${props.product.name}`
  window.open(`https://wa.me/?text=${encodeURIComponent(message)}`, '_blank')
}
</script>

<template>
  <div class="min-h-screen bg-neutral-50">
    <Head>
      <title>{{ product.name }} - {{ atelier.name }}</title>
      <meta name="description" :content="product.short_desc || product.name">
    </Head>

    <!-- Header/Navigation -->
    <header class="bg-white border-b sticky top-0 z-50">
      <div class="mx-auto px-[4vw] max-w-[90rem] flex items-center justify-between" style="padding-top: clamp(1rem, 2vh, 1.5rem); padding-bottom: clamp(1rem, 2vh, 1.5rem);">
        <button @click="goBack" class="flex items-center gap-2 text-neutral-700 hover:text-black transition">
          <ArrowLeft style="width: clamp(1.25rem, 2vw, 1.5rem); height: clamp(1.25rem, 2vw, 1.5rem);" />
          <span style="font-size: clamp(0.875rem, 1.1vw, 1rem);">Voltar</span>
        </button>

        <div class="flex items-center gap-3">
          <img v-if="atelier.logo_url" :src="atelier.logo_url" :alt="atelier.name" class="rounded-md object-cover" style="width: clamp(2rem, 3vw, 2.5rem); height: clamp(2rem, 3vw, 2.5rem);">
          <span class="font-bold" style="font-size: clamp(1rem, 1.5vw, 1.25rem);">{{ atelier.name }}</span>
        </div>

        <div class="flex items-center" style="gap: clamp(0.5rem, 1vw, 1rem);">
          <button class="p-2 rounded-full hover:bg-neutral-100 transition" aria-label="Compartilhar">
            <Share2 style="width: clamp(1.25rem, 1.5vw, 1.5rem); height: clamp(1.25rem, 1.5vw, 1.5rem);" />
          </button>
          <button class="p-2 rounded-full hover:bg-neutral-100 transition" aria-label="Favoritar">
            <Heart style="width: clamp(1.25rem, 1.5vw, 1.5rem); height: clamp(1.25rem, 1.5vw, 1.5rem);" />
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="mx-auto px-[4vw] max-w-[90rem] grid lg:grid-cols-2" style="padding-top: clamp(2rem, 4vh, 3rem); padding-bottom: clamp(3rem, 6vh, 5rem); gap: clamp(3rem, 6vw, 6rem);">

      <!-- Gallery -->
      <div>
        <div class="aspect-square rounded-2xl overflow-hidden bg-neutral-100 mb-[2vh]">
          <img :src="mainImage" :alt="product.name" class="w-full h-full object-cover">
        </div>

        <div v-if="product.photos.length > 1" class="flex overflow-x-auto" style="gap: clamp(0.75rem, 1.5vw, 1.25rem); padding-bottom: clamp(0.5rem, 1vh, 0.75rem);">
          <button
            v-for="(photo, index) in product.photos"
            :key="photo.id"
            @click="selectedImageIndex = index"
            class="flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all"
            :class="selectedImageIndex === index ? 'border-black' : 'border-transparent opacity-60 hover:opacity-100'"
            style="width: clamp(4rem, 8vw, 6rem); height: clamp(4rem, 8vw, 6rem);"
          >
            <img :src="photo.thumb" :alt="`${product.name} ${index + 1}`" class="w-full h-full object-cover">
          </button>
        </div>
      </div>

      <!-- Product Info -->
      <div class="flex flex-col">
        <div class="mb-[2vh]">
          <span v-if="product.category" class="inline-block px-3 py-1 text-xs font-medium bg-neutral-100 text-neutral-700 rounded-full mb-[2vh]">
            {{ product.category.name }}
          </span>

          <h1 class="font-bold leading-tight mb-[1.5vh]" style="font-size: clamp(1.75rem, 4vw, 3rem);">
            {{ product.name }}
          </h1>

          <p v-if="product.short_desc" class="text-neutral-600 leading-relaxed" style="font-size: clamp(0.875rem, 1.1vw, 1.125rem);">
            {{ product.short_desc }}
          </p>
        </div>

        <div class="mb-[3vh]">
          <div class="font-bold" style="font-size: clamp(2rem, 4vw, 3rem);">
            {{ fmtMoney(product.price, product.currency) }}
          </div>
        </div>

        <div v-if="product.long_desc" class="mb-[3vh] p-[2vh] bg-neutral-50 rounded-xl border">
          <h3 class="font-semibold mb-[1vh]" style="font-size: clamp(1rem, 1.2vw, 1.125rem);">Descrição</h3>
          <p class="text-neutral-700 whitespace-pre-line" style="font-size: clamp(0.875rem, 1vw, 1rem);">
            {{ product.long_desc }}
          </p>
        </div>

        <div v-if="product.is_personalizable" class="mb-[3vh] p-[2vh] bg-blue-50 rounded-xl border border-blue-200">
          <div class="flex items-center gap-2 mb-[1vh]">
            <span class="text-2xl">✨</span>
            <h3 class="font-semibold text-blue-900" style="font-size: clamp(1rem, 1.2vw, 1.125rem);">Produto Personalizável</h3>
          </div>
          <p class="text-blue-800" style="font-size: clamp(0.875rem, 1vw, 1rem);">
            Este produto pode ser personalizado de acordo com suas preferências. Entre em contato para mais detalhes.
          </p>
        </div>

        <div class="flex flex-col" style="gap: clamp(1rem, 2vh, 1.5rem); margin-top: auto;">
          <button @click="contactWhatsApp" class="w-full bg-black text-white font-semibold rounded-xl hover:bg-neutral-800 transition" style="padding: clamp(1rem, 2vh, 1.25rem); font-size: clamp(1rem, 1.2vw, 1.125rem);">
            Entrar em contato via WhatsApp
          </button>

          <a :href="`/${atelier.slug}`" class="w-full text-center border-2 border-black text-black font-semibold rounded-xl hover:bg-black hover:text-white transition" style="padding: clamp(1rem, 2vh, 1.25rem); font-size: clamp(1rem, 1.2vw, 1.125rem);">
            Ver mais produtos do ateliê
          </a>
        </div>
      </div>
    </main>
  </div>
</template>
