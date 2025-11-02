<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Separator } from '@/components/ui/separator'
import { Eye, Upload, Save, Palette, Image as ImageIcon, FileText, Mail } from 'lucide-vue-next'

const props = defineProps<{
  lpConfig: {
    id: number
    data: any
    draft: any
  }
  atelier: any
}>()

// Initialize with default values
const defaultTheme = {
  primaryColor: '#000000',
  secondaryColor: '#ffffff',
  accentColor: '#3b82f6',
  backgroundColor: '#ffffff',
  textColor: '#1f2937',
}

const form = useForm({
  data: {
    hero_image_url: props.lpConfig.data?.hero_image_url || '',
    cta_label: props.lpConfig.data?.cta_label || '',
    cta_link: props.lpConfig.data?.cta_link || '#products',
    story_title: props.lpConfig.data?.story_title || '',
    story_html: props.lpConfig.data?.story_html || '',
    whatsapp: props.lpConfig.data?.whatsapp || '',
    email: props.lpConfig.data?.email || '',
    instagram: props.lpConfig.data?.instagram || '',
    instagram_url: props.lpConfig.data?.instagram_url || '',
    address: props.lpConfig.data?.address || '',
    facebook: props.lpConfig.data?.facebook || '',
    theme: {
      ...defaultTheme,
      ...props.lpConfig.data?.theme,
    }
  },
  status: 'published',
})

// Debug: Verificar o que foi carregado
console.log('LP Config Data:', props.lpConfig.data)
console.log('Form Data:', form.data)
console.log('Theme:', form.data.theme)

const uploadingImage = ref(false)
const heroImagePreview = ref(props.lpConfig.data?.hero_image_url || '')

function save() {
  form.put('/lp-config', {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Erro ao salvar:', errors)
      // Se for erro 419, recarrega a página para obter novo token CSRF
      if (Object.values(errors).some(err => String(err).includes('419'))) {
        window.location.reload()
      }
    },
  })
}

async function uploadHeroImage(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  uploadingImage.value = true
  const formData = new FormData()
  formData.append('image', file)
  formData.append('type', 'hero')

  try {
    const response = await fetch('/lp-config/upload-image', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: formData,
    })

    if (response.status === 419) {
      alert('Sessão expirada. A página será recarregada.')
      window.location.reload()
      return
    }

    const data = await response.json()

    if (!response.ok) {
      // Erro do servidor
      const errorMsg = data.message || data.error || 'Erro ao fazer upload da imagem'
      console.error('Erro do servidor:', data)
      alert(errorMsg)
      return
    }

    if (data.url) {
      form.data.hero_image_url = data.url
      heroImagePreview.value = data.url
      alert('Imagem enviada com sucesso!')
    }
  } catch (error) {
    console.error('Erro ao fazer upload:', error)
    alert('Erro ao fazer upload da imagem. Verifique se o arquivo é uma imagem válida (máx 5MB).')
  } finally {
    uploadingImage.value = false
  }
}

function openPreview() {
  window.open(`/${props.atelier.slug}`, '_blank')
}
</script>

<template>
  <AppLayout title="Configurar Landing Page">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6">
          <!-- Header -->
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="space-y-1">
          <h1 class="text-3xl font-bold tracking-tight">Configurar Landing Page</h1>
          <p class="text-muted-foreground">Personalize a aparência e conteúdo da sua página pública</p>
        </div>
        <div class="flex items-center gap-2">
          <Button
            type="button"
            variant="outline"
            @click="openPreview"
            class="gap-2"
          >
            <Eye class="h-4 w-4" />
            Visualizar
          </Button>
          <Button
            @click="save"
            :disabled="form.processing"
            class="gap-2"
          >
            <Save class="h-4 w-4" />
            {{ form.processing ? 'Salvando...' : 'Salvar' }}
          </Button>
        </div>
      </div>

      <!-- Error messages -->
      <div v-if="form.hasErrors" class="rounded-lg border border-destructive/50 bg-destructive/10 p-4">
        <div class="flex items-start gap-3">
          <div class="text-destructive">⚠️</div>
          <div>
            <h3 class="font-semibold text-destructive">Erro ao salvar</h3>
            <p class="text-sm text-destructive/90 mt-1">
              Houve um problema ao salvar as alterações. Se o erro persistir, recarregue a página.
            </p>
          </div>
        </div>
      </div>

      <!-- Success message -->
      <div v-if="form.recentlySuccessful" class="rounded-lg border border-green-500/50 bg-green-500/10 p-4">
        <div class="flex items-start gap-3">
          <div class="text-green-600">✓</div>
          <div>
            <h3 class="font-semibold text-green-600">Salvo com sucesso!</h3>
            <p class="text-sm text-green-600/90 mt-1">
              As alterações da sua landing page foram salvas.
            </p>
          </div>
        </div>
      </div>

      <Separator />

      <Tabs default-value="hero" class="space-y-6">
        <TabsList class="grid w-full grid-cols-4">
          <TabsTrigger value="hero" class="gap-2">
            <ImageIcon class="h-4 w-4" />
            Hero
          </TabsTrigger>
          <TabsTrigger value="story" class="gap-2">
            <FileText class="h-4 w-4" />
            História
          </TabsTrigger>
          <TabsTrigger value="contact" class="gap-2">
            <Mail class="h-4 w-4" />
            Contato
          </TabsTrigger>
          <TabsTrigger value="theme" class="gap-2">
            <Palette class="h-4 w-4" />
            Tema
          </TabsTrigger>
        </TabsList>

        <!-- Hero Tab -->
        <TabsContent value="hero" class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Seção Principal (Hero)</CardTitle>
              <CardDescription>Configure o banner principal da sua landing page</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
              <div class="space-y-4">
                <div class="space-y-2">
                  <Label for="hero_image_file" class="flex items-center gap-2">
                    <Upload class="h-4 w-4" />
                    Imagem de Fundo
                  </Label>
                  <Input
                    id="hero_image_file"
                    type="file"
                    @change="uploadHeroImage"
                    accept="image/*"
                    :disabled="uploadingImage"
                  />
                  <p class="text-xs text-muted-foreground">PNG, JPG, GIF ou WebP (máx. 5MB)</p>
                </div>

                <div class="space-y-2">
                  <Label for="hero_image_url">URL da Imagem</Label>
                  <Input
                    id="hero_image_url"
                    v-model="form.data.hero_image_url"
                    placeholder="https://exemplo.com/imagem.jpg"
                    @input="heroImagePreview = form.data.hero_image_url"
                  />
                </div>

                <div v-if="heroImagePreview" class="rounded-lg border overflow-hidden">
                  <img :src="heroImagePreview" alt="Preview" class="w-full h-48 object-cover">
                </div>
              </div>

              <Separator />

              <div class="space-y-2">
                <Label for="cta_label">Texto do Botão CTA</Label>
                <Input
                  id="cta_label"
                  v-model="form.data.cta_label"
                  placeholder="Ex: Explorar Produtos"
                />
                <p class="text-xs text-muted-foreground">Texto do call-to-action principal</p>
              </div>

              <div class="space-y-2">
                <Label for="cta_link">Link do Botão</Label>
                <Input
                  id="cta_link"
                  v-model="form.data.cta_link"
                  placeholder="#products"
                />
                <p class="text-xs text-muted-foreground">Use #products ou uma URL externa</p>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- Story Tab -->
        <TabsContent value="story" class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Nossa História</CardTitle>
              <CardDescription>Conte a história do seu ateliê e conecte-se com seus clientes</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
              <div class="space-y-2">
                <Label for="story_title">Título da Seção</Label>
                <Input
                  id="story_title"
                  v-model="form.data.story_title"
                  placeholder="Ex: Nossa História"
                />
              </div>

              <div class="space-y-2">
                <Label for="story_html">Conteúdo</Label>
                <Textarea
                  id="story_html"
                  v-model="form.data.story_html"
                  rows="12"
                  placeholder="Conte a história do seu ateliê..."
                  class="font-mono text-sm"
                />
                <p class="text-xs text-muted-foreground">Aceita HTML: &lt;p&gt;, &lt;strong&gt;, &lt;br&gt;</p>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- Contact Tab -->
        <TabsContent value="contact" class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Informações de Contato</CardTitle>
              <CardDescription>Configure como os clientes podem te encontrar</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-2">
                  <Label for="whatsapp">WhatsApp</Label>
                  <Input
                    id="whatsapp"
                    v-model="form.data.whatsapp"
                    placeholder="(84) 99999-9999"
                  />
                </div>

                <div class="space-y-2">
                  <Label for="email">Email</Label>
                  <Input
                    id="email"
                    v-model="form.data.email"
                    type="email"
                    placeholder="contato@seuatelie.com"
                  />
                </div>

                <div class="space-y-2">
                  <Label for="instagram">Instagram (usuário)</Label>
                  <Input
                    id="instagram"
                    v-model="form.data.instagram"
                    placeholder="@seuatelie"
                  />
                </div>

                <div class="space-y-2">
                  <Label for="instagram_url">Instagram (URL)</Label>
                  <Input
                    id="instagram_url"
                    v-model="form.data.instagram_url"
                    placeholder="https://instagram.com/seuatelie"
                  />
                </div>

                <div class="space-y-2">
                  <Label for="address">Endereço</Label>
                  <Input
                    id="address"
                    v-model="form.data.address"
                    placeholder="Rua Principal, 123 - Cidade/UF"
                  />
                </div>

                <div class="space-y-2">
                  <Label for="facebook">Facebook (URL)</Label>
                  <Input
                    id="facebook"
                    v-model="form.data.facebook"
                    placeholder="https://facebook.com/seuatelie"
                  />
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- Theme Tab -->
        <TabsContent value="theme" class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Cores e Tema</CardTitle>
              <CardDescription>Personalize as cores da sua landing page</CardDescription>
            </CardHeader>
            <CardContent>
              <div v-if="!form.data.theme" class="text-center py-8">
                <p class="text-muted-foreground">Carregando configurações de tema...</p>
              </div>
              <div v-else class="grid gap-6 md:grid-cols-2">
                <div class="space-y-3">
                  <Label for="primaryColor">Cor Primária</Label>
                  <div class="flex gap-3">
                    <Input
                      id="primaryColor"
                      type="color"
                      v-model="form.data.theme.primaryColor"
                      class="h-10 w-20 cursor-pointer"
                    />
                    <Input
                      v-model="form.data.theme.primaryColor"
                      placeholder="#000000"
                      class="flex-1 font-mono"
                    />
                  </div>
                  <p class="text-xs text-muted-foreground">Botões principais e CTAs</p>
                </div>

                <div class="space-y-3">
                  <Label for="secondaryColor">Cor Secundária</Label>
                  <div class="flex gap-3">
                    <Input
                      id="secondaryColor"
                      type="color"
                      v-model="form.data.theme.secondaryColor"
                      class="h-10 w-20 cursor-pointer"
                    />
                    <Input
                      v-model="form.data.theme.secondaryColor"
                      placeholder="#ffffff"
                      class="flex-1 font-mono"
                    />
                  </div>
                  <p class="text-xs text-muted-foreground">Fundos e contraste</p>
                </div>

                <div class="space-y-3">
                  <Label for="accentColor">Cor de Destaque</Label>
                  <div class="flex gap-3">
                    <Input
                      id="accentColor"
                      type="color"
                      v-model="form.data.theme.accentColor"
                      class="h-10 w-20 cursor-pointer"
                    />
                    <Input
                      v-model="form.data.theme.accentColor"
                      placeholder="#3b82f6"
                      class="flex-1 font-mono"
                    />
                  </div>
                  <p class="text-xs text-muted-foreground">Links e badges</p>
                </div>

                <div class="space-y-3">
                  <Label for="backgroundColor">Cor de Fundo</Label>
                  <div class="flex gap-3">
                    <Input
                      id="backgroundColor"
                      type="color"
                      v-model="form.data.theme.backgroundColor"
                      class="h-10 w-20 cursor-pointer"
                    />
                    <Input
                      v-model="form.data.theme.backgroundColor"
                      placeholder="#ffffff"
                      class="flex-1 font-mono"
                    />
                  </div>
                  <p class="text-xs text-muted-foreground">Background geral</p>
                </div>

                <div class="space-y-3 md:col-span-2">
                  <Label for="textColor">Cor do Texto</Label>
                  <div class="flex gap-3">
                    <Input
                      id="textColor"
                      type="color"
                      v-model="form.data.theme.textColor"
                      class="h-10 w-20 cursor-pointer"
                    />
                    <Input
                      v-model="form.data.theme.textColor"
                      placeholder="#1f2937"
                      class="flex-1 font-mono"
                    />
                  </div>
                  <p class="text-xs text-muted-foreground">Textos principais e títulos</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
