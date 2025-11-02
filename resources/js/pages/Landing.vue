<script setup>
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
  atelier: Object,
  contact: Object,
  featured_products: Array,
  year: Number,
  social: Object
})

const heroStyle = computed(() =>
  props.atelier?.hero_image
    ? { backgroundImage: `url(${props.atelier.hero_image})`, backgroundSize: 'cover', backgroundPosition: 'center' }
    : {}
)

const fmtMoney = (value, currency = 'BRL', locale = 'pt-BR') =>
  new Intl.NumberFormat(locale, { style: 'currency', currency }).format(value ?? 0)

// Inertia form helper (CSRF e erros automáticos)
const form = useForm({ name: '', email: '', message: '' })

function sendContact() {
  form.post(route('lp.contact'), {
    preserveScroll: true,
    onSuccess: () => form.reset()
  })
}
</script>

<template>
  <div>
    <Head>
      <title>{{ props.atelier?.name ?? 'Artisan Workshop' }}</title>
      <meta name="description" :content="props.atelier?.tagline ?? ''">
    </Head>

    <!-- HERO -->
    <section class="relative h-[420px] md:h-[520px] flex items-center justify-center text-center" :style="heroStyle">
      <div class="absolute inset-0 bg-black/40" v-if="props.atelier?.hero_image"></div>
      <div class="relative z-10 max-w-4xl px-4">
        <h1 class="text-4xl md:text-6xl font-extrabold text-white drop-shadow">
          {{ props.atelier?.name ?? 'Artisan Workshop' }}
        </h1>
        <p class="mt-3 text-white/90">
          {{ props.atelier?.tagline ?? 'Peças únicas com técnicas atemporais.' }}
        </p>
        <a :href="props.atelier?.cta_link || '#products'"
           class="inline-flex mt-6 px-5 py-3 rounded-xl bg-black text-white font-medium hover:opacity-90">
          {{ props.atelier?.cta_label || 'Explorar Produtos' }}
        </a>
      </div>
    </section>

    <!-- FEATURED -->
    <section id="products" class="max-w-6xl mx-auto px-4 py-12">
      <h2 class="text-2xl md:text-3xl font-bold">Produtos em destaque</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
        <div v-for="p in props.featured_products" :key="p.id"
             class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden hover:shadow-md transition">
          <img :src="p.image" alt="" class="w-full h-60 object-cover">
          <div class="p-4">
            <div class="text-sm">
              <span v-if="p.badge" class="px-2 py-0.5 text-xs rounded-full bg-neutral-100 border">{{ p.badge }}</span>
            </div>
            <h3 class="mt-2 font-semibold text-neutral-900">{{ p.name }}</h3>
            <p class="text-sm text-neutral-600 line-clamp-2 mt-1">{{ p.excerpt }}</p>
            <div class="mt-3 font-semibold">{{ fmtMoney(p.price, p.currency || 'BRL') }}</div>
          </div>
        </div>

        <div v-if="!props.featured_products?.length" class="col-span-full text-neutral-500">
          Nenhum produto em destaque no momento.
        </div>
      </div>
    </section>

    <!-- STORY -->
    <section class="max-w-6xl mx-auto px-4 py-12 grid md:grid-cols-2 gap-8 items-center">
      <div class="aspect-video rounded-2xl overflow-hidden bg-neutral-200"></div>
      <div>
        <h3 class="text-2xl font-bold">{{ props.atelier?.story_title || 'Nossa História' }}</h3>
        <div class="prose prose-neutral mt-3" v-html="props.atelier?.story_html"></div>
        <a :href="props.atelier?.cta_link || '#products'" class="inline-flex mt-4 px-4 py-2 rounded-lg bg-black text-white hover:opacity-90">
          Saiba mais
        </a>
      </div>
    </section>

    <!-- CONTACT -->
    <section class="max-w-6xl mx-auto px-4 py-12">
      <h3 class="text-xl md:text-2xl font-bold">Fale conosco</h3>
      <div class="grid md:grid-cols-2 gap-8 mt-6">
        <div class="space-y-5">
          <div>
            <div class="text-sm text-neutral-500">WhatsApp</div>
            <div class="font-medium">{{ props.contact?.whatsapp }}</div>
          </div>
          <div>
            <div class="text-sm text-neutral-500">Email</div>
            <div class="font-medium">{{ props.contact?.email }}</div>
          </div>
          <div>
            <div class="text-sm text-neutral-500">Instagram</div>
            <div class="font-medium">{{ props.contact?.instagram }}</div>
          </div>
          <div>
            <div class="text-sm text-neutral-500">Endereço</div>
            <div class="font-medium">{{ props.contact?.address }}</div>
          </div>
        </div>

        <form @submit.prevent="sendContact" class="bg-white rounded-2xl border p-4 md:p-6 space-y-3">
          <input v-model="form.name" class="w-full border rounded-lg px-3 py-2" placeholder="Seu nome" required>
          <input v-model="form.email" class="w-full border rounded-lg px-3 py-2" placeholder="Seu e-mail" type="email" required>
          <textarea v-model="form.message" class="w-full border rounded-lg px-3 py-2 min-h-[120px]" placeholder="Mensagem" required />
          <button :disabled="form.processing" class="px-4 py-2 rounded-lg bg-black text-white disabled:opacity-60">
            {{ form.processing ? 'Enviando...' : 'Enviar' }}
          </button>

          <div v-if="$page.props.flash?.lp_contact_ok" class="text-green-600 text-sm mt-2">
            Mensagem enviada com sucesso!
          </div>

          <div v-if="form.errors && Object.keys(form.errors).length" class="text-red-600 text-sm mt-2">
            <div v-for="(msg, field) in form.errors" :key="field">{{ msg }}</div>
          </div>
        </form>
      </div>
    </section>

    <footer class="py-10 text-center text-sm text-neutral-500 border-t">
      © {{ props.year }} {{ props.atelier?.name || 'Artisan Workshop' }}. Todos os direitos reservados.
    </footer>
  </div>
</template>

<style scoped>
.prose :where(p):not(:where([class~="not-prose"] *)) { margin: 0.5rem 0; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
