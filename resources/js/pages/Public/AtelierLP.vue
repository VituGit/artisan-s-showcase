<script setup lang="ts">
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

declare const route: any

const props = defineProps<{
  atelier?: any,
  contact?: any,
  theme?: {
    primaryColor?: string
    secondaryColor?: string
    accentColor?: string
    backgroundColor?: string
    textColor?: string
  },
  featured_products?: any[],
  year: number,
  social?: any
}>()

const heroStyle = computed(() =>
  (props.atelier as any)?.hero_image
    ? { backgroundImage: `url(${(props.atelier as any).hero_image})`, backgroundSize: 'cover', backgroundPosition: 'center' }
    : {}
)

const themeVars = computed(() => ({
  '--primary-color': props.theme?.primaryColor || '#000000',
  '--secondary-color': props.theme?.secondaryColor || '#ffffff',
  '--accent-color': props.theme?.accentColor || '#3b82f6',
  '--bg-color': props.theme?.backgroundColor || '#ffffff',
  '--text-color': props.theme?.textColor || '#1f2937',
}))

const fmtMoney = (value: number, currency = 'BRL', locale = 'pt-BR') =>
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
  <div :style="themeVars">
    <Head>
      <title>{{ props.atelier?.name ?? 'Artisan Workshop' }}</title>
      <meta name="description" :content="props.atelier?.tagline ?? ''">
    </Head>

    <!-- HERO -->
    <section class="relative flex items-center" :style="{ ...heroStyle, minHeight: '50vh', height: 'clamp(50vh, 60vh, 70vh)' }">
      <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/10"></div>
      <div class="relative z-10 w-full mx-auto px-[4vw] max-w-[90rem] flex items-center gap-[2vw]">
        <div class="flex-1 text-white">
          <div class="flex items-center gap-[1.5vw]">
            <img v-if="props.atelier?.logo_url" :src="props.atelier.logo_url" alt="logo" class="rounded-md object-cover border border-white/20 shadow-sm" style="width: clamp(4rem, 6vw, 6rem); height: clamp(4rem, 6vw, 6rem);">
            <div>
              <h1 class="font-extrabold leading-tight" style="font-size: clamp(1.75rem, 4vw, 3.5rem);">{{ props.atelier?.name ?? 'Artisan Workshop' }}</h1>
              <div class="mt-[0.5vh] text-white/90" style="font-size: clamp(0.875rem, 1.2vw, 1.125rem);">{{ props.atelier?.tagline ?? 'Peças únicas com técnicas atemporais.' }}</div>
              <div v-if="props.atelier?.city || props.atelier?.state" class="mt-[1vh] text-white/80" style="font-size: clamp(0.75rem, 1vw, 0.875rem);">{{ [props.atelier?.city, props.atelier?.state].filter(Boolean).join(' / ') }}</div>
            </div>
          </div>

          <div class="mt-[3vh]">
            <a href="#products" class="themed-btn-primary inline-flex rounded-xl font-medium shadow hover:opacity-95" style="padding: clamp(0.75rem, 1.5vh, 1rem) clamp(1.25rem, 2vw, 1.75rem);">
              {{ props.atelier?.cta_label || 'Explorar Produtos' }}
            </a>
            <a href="#contact" class="ml-[1vw] inline-flex rounded-xl border border-white/20 text-white hover:bg-white/10" style="padding: clamp(0.75rem, 1.5vh, 1rem) clamp(1rem, 1.5vw, 1.5rem);">Fale conosco</a>
          </div>
        </div>
        <div class="hidden md:block" style="width: clamp(20rem, 30vw, 35rem);">
          <div class="aspect-[4/3] rounded-2xl overflow-hidden border border-white/10 bg-neutral-100">
            <img v-if="props.atelier?.hero_image" :src="props.atelier.hero_image" alt="hero" class="w-full h-full object-cover">
            <div v-else class="flex items-center justify-center h-full text-neutral-400" style="font-size: clamp(0.875rem, 1vw, 1rem);">Sem imagem de capa</div>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURED -->
    <section id="products" class="mx-auto px-[4vw] max-w-[90rem]" style="padding-top: clamp(3rem, 6vh, 5rem); padding-bottom: clamp(3rem, 6vh, 5rem);">
      <div class="flex items-center justify-between">
        <h2 class="font-bold" style="font-size: clamp(1.5rem, 3vw, 2.25rem);">Produtos em destaque</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mt-[3vh]" style="gap: clamp(1.25rem, 2vw, 2rem);">
        <a v-for="p in props.featured_products" :key="p.id" :href="`/ateliers/${props.atelier?.slug}/products/${p.slug}`" class="group block bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden hover:shadow-md transform hover:-translate-y-1 transition">
          <div class="relative w-full overflow-hidden bg-neutral-100" style="height: clamp(12rem, 20vh, 16rem);">
            <img :src="p.image" alt="" class="w-full h-full object-cover transition-transform group-hover:scale-105" loading="lazy">
            <div v-if="p.badge" class="absolute rounded-full bg-white/90 text-neutral-800 font-medium border" style="top: clamp(0.75rem, 1.5vh, 1rem); left: clamp(0.75rem, 1.5vw, 1rem); padding: clamp(0.25rem, 0.5vh, 0.375rem) clamp(0.5rem, 1vw, 0.75rem); font-size: clamp(0.75rem, 0.9vw, 0.875rem);">{{ p.badge }}</div>
            <div class="absolute bg-black/70 text-white rounded-md font-semibold" style="right: clamp(0.75rem, 1.5vw, 1rem); bottom: clamp(0.75rem, 1.5vh, 1rem); padding: clamp(0.25rem, 0.5vh, 0.375rem) clamp(0.75rem, 1.2vw, 1rem); font-size: clamp(0.875rem, 1vw, 1rem);">{{ fmtMoney(p.price, p.currency || 'BRL') }}</div>
          </div>
          <div style="padding: clamp(1rem, 2vh, 1.5rem);">
            <h3 class="font-semibold text-neutral-900 truncate" style="font-size: clamp(1rem, 1.2vw, 1.125rem);">{{ p.name }}</h3>
            <p class="text-neutral-600 line-clamp-2" style="margin-top: clamp(0.25rem, 0.5vh, 0.5rem); font-size: clamp(0.875rem, 1vw, 1rem);">{{ p.excerpt }}</p>
          </div>
        </a>

        <div v-if="!props.featured_products?.length" class="col-span-full text-neutral-500">
          Nenhum produto em destaque no momento.
        </div>
      </div>
    </section>

    <!-- STORY -->
    <section class="mx-auto px-[4vw] max-w-[90rem] grid md:grid-cols-2 items-center" style="padding-top: clamp(3rem, 6vh, 5rem); padding-bottom: clamp(3rem, 6vh, 5rem); gap: clamp(2rem, 4vw, 4rem);">
      <div class="aspect-video rounded-2xl overflow-hidden bg-neutral-200"></div>
      <div>
        <h3 class="font-bold themed-text" style="font-size: clamp(1.5rem, 2.5vw, 2rem);">{{ props.atelier?.story_title || 'Nossa História' }}</h3>
        <div class="prose prose-neutral themed-text" style="margin-top: clamp(0.75rem, 1.5vh, 1.25rem); font-size: clamp(0.875rem, 1.1vw, 1rem);" v-html="props.atelier?.story_html"></div>
        <a href="#contact" class="themed-btn-primary inline-flex rounded-lg hover:opacity-90" style="margin-top: clamp(1rem, 2vh, 1.5rem); padding: clamp(0.5rem, 1vh, 0.75rem) clamp(1rem, 2vw, 1.5rem); font-size: clamp(0.875rem, 1vw, 1rem);">
          Entre em contato
        </a>
      </div>
    </section>

    <!-- CONTACT -->
    <section id="contact" class="mx-auto px-[4vw] max-w-[90rem]" style="padding-top: clamp(3rem, 6vh, 5rem); padding-bottom: clamp(3rem, 6vh, 5rem);">
      <h3 class="font-bold" style="font-size: clamp(1.25rem, 2.5vw, 2rem);">Fale conosco</h3>
      <div class="grid md:grid-cols-2 mt-[3vh]" style="gap: clamp(2rem, 4vw, 4rem);">
        <div style="display: flex; flex-direction: column; gap: clamp(1.25rem, 2.5vh, 2rem);">
          <div>
            <div class="text-neutral-500" style="font-size: clamp(0.875rem, 1vw, 1rem);">WhatsApp</div>
            <div class="font-medium" style="font-size: clamp(1rem, 1.2vw, 1.125rem);">{{ props.contact?.whatsapp }}</div>
          </div>
          <div>
            <div class="text-neutral-500" style="font-size: clamp(0.875rem, 1vw, 1rem);">Email</div>
            <div class="font-medium" style="font-size: clamp(1rem, 1.2vw, 1.125rem);">{{ props.contact?.email }}</div>
          </div>
          <div>
            <div class="text-neutral-500" style="font-size: clamp(0.875rem, 1vw, 1rem);">Instagram</div>
            <div class="font-medium" style="font-size: clamp(1rem, 1.2vw, 1.125rem);">{{ props.contact?.instagram }}</div>
          </div>
          <div>
            <div class="text-neutral-500" style="font-size: clamp(0.875rem, 1vw, 1rem);">Endereço</div>
            <div class="font-medium" style="font-size: clamp(1rem, 1.2vw, 1.125rem);">{{ props.contact?.address }}</div>
          </div>
        </div>

        <form @submit.prevent="sendContact" class="bg-white rounded-2xl border" style="padding: clamp(1rem, 2vh, 1.5rem); display: flex; flex-direction: column; gap: clamp(0.75rem, 1.5vh, 1.25rem);">
          <input v-model="form.name" class="w-full border rounded-lg" style="padding: clamp(0.5rem, 1vh, 0.75rem) clamp(0.75rem, 1.5vw, 1rem); font-size: clamp(0.875rem, 1vw, 1rem);" placeholder="Seu nome" required>
          <input v-model="form.email" class="w-full border rounded-lg" style="padding: clamp(0.5rem, 1vh, 0.75rem) clamp(0.75rem, 1.5vw, 1rem); font-size: clamp(0.875rem, 1vw, 1rem);" placeholder="Seu e-mail" type="email" required>
          <textarea v-model="form.message" class="w-full border rounded-lg" style="padding: clamp(0.5rem, 1vh, 0.75rem) clamp(0.75rem, 1.5vw, 1rem); min-height: clamp(8rem, 15vh, 12rem); font-size: clamp(0.875rem, 1vw, 1rem);" placeholder="Mensagem" required></textarea>
          <div class="flex items-center" style="gap: clamp(0.75rem, 1.5vw, 1.25rem);">
            <button :disabled="form.processing" class="themed-btn-primary rounded-lg disabled:opacity-60" style="padding: clamp(0.5rem, 1vh, 0.75rem) clamp(1rem, 2vw, 1.5rem); font-size: clamp(0.875rem, 1vw, 1rem);">
              {{ form.processing ? 'Enviando...' : 'Enviar' }}
            </button>
            <div v-if="props.social?.instagram" class="text-neutral-600" style="font-size: clamp(0.875rem, 1vw, 1rem);">Siga: <a :href="props.social.instagram" class="themed-accent hover:underline">Instagram</a></div>
          </div>

          <div v-if="($page.props.flash as any)?.lp_contact_ok" class="text-green-600" style="font-size: clamp(0.875rem, 1vw, 1rem);">
            Mensagem enviada com sucesso!
          </div>

          <div v-if="form.errors && Object.keys(form.errors).length" class="text-red-600" style="font-size: clamp(0.875rem, 1vw, 1rem);">
            <div v-for="(msg, field) in form.errors" :key="field">{{ msg }}</div>
          </div>
        </form>
      </div>
    </section>

    <footer class="text-center text-neutral-500 border-t" style="padding-top: clamp(2.5rem, 5vh, 4rem); padding-bottom: clamp(2.5rem, 5vh, 4rem); font-size: clamp(0.875rem, 1vw, 1rem);">
      © {{ props.year }} {{ props.atelier?.name || 'Artisan Workshop' }}. Todos os direitos reservados.
    </footer>
  </div>
</template>

<style scoped>
.prose :where(p):not(:where([class~="not-prose"] *)) { margin: 0.5rem 0; }
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
}

/* Themed components using CSS variables */
.themed-btn-primary {
  background-color: var(--primary-color, #000);
  color: var(--secondary-color, #fff);
}

.themed-btn-secondary {
  background-color: var(--secondary-color, #fff);
  color: var(--primary-color, #000);
}

.themed-text {
  color: var(--text-color, #1f2937);
}

.themed-bg {
  background-color: var(--bg-color, #ffffff);
}

.themed-accent {
  color: var(--accent-color, #3b82f6);
}
</style>
