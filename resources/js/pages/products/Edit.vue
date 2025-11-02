<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft, Upload, X, Star } from 'lucide-vue-next';

interface Category {
  id: number;
  name: string;
}

interface ProductPhoto {
  id: number;
  file_path: string;
  thumb_path: string | null;
  medium_path: string | null;
  is_cover: boolean;
  position: number;
}

interface Product {
  id: number;
  name: string;
  slug: string;
  short_desc: string | null;
  description: string | null;
  price: string;
  is_personalizable: boolean;
  personalization_hint: string | null;
  is_available: boolean;
  is_featured: boolean;
  position: number;
  category: {
    id: number;
    name: string;
  } | null;
  photos: ProductPhoto[];
}

const props = defineProps<{
  product: Product;
  categories: Category[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Produtos',
    href: '/products',
  },
  {
    title: props.product.name,
    href: `/products/${props.product.slug}/edit`,
  },
];

const form = ref({
  category_id: props.product.category?.id.toString() || '',
  name: props.product.name,
  short_desc: props.product.short_desc || '',
  description: props.product.description || '',
  price: props.product.price,
  is_personalizable: props.product.is_personalizable,
  personalization_hint: props.product.personalization_hint || '',
  is_available: props.product.is_available,
  is_featured: props.product.is_featured,
  position: props.product.position,
});

const errors = ref<Record<string, string>>({});
const photoInput = ref<HTMLInputElement | null>(null);
const uploadingPhotos = ref(false);

const submit = () => {
  router.put(`/products/${props.product.slug}`, form.value, {
    onError: (err) => {
      errors.value = err;
    },
    onSuccess: () => {
      errors.value = {};
    }
  });
};

const selectPhotos = () => {
  photoInput.value?.click();
};

const uploadPhotos = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const files = target.files;

  if (!files || files.length === 0) return;

  const formData = new FormData();
  for (let i = 0; i < files.length; i++) {
    formData.append('photos[]', files[i]);
  }

  uploadingPhotos.value = true;

  router.post(`/products/${props.product.slug}/photos`, formData, {
    onSuccess: () => {
      uploadingPhotos.value = false;
      if (photoInput.value) {
        photoInput.value.value = '';
      }
    },
    onError: () => {
      uploadingPhotos.value = false;
    }
  });
};

const deletePhoto = (photoId: number) => {
  if (confirm('Tem certeza que deseja excluir esta foto?')) {
    router.delete(`/products/${props.product.slug}/photos/${photoId}`);
  }
};

const setCover = (photoId: number) => {
  router.post(`/products/${props.product.slug}/photos/${photoId}/cover`, {});
};

const getPhotoUrl = (photo: ProductPhoto) => {
  return `/storage/${photo.thumb_path || photo.file_path}`;
};
</script>

<template>
  <Head title="Editar Produto" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
          <Link href="/products">
            <Button variant="ghost" size="sm">
              <ArrowLeft class="mr-2 h-4 w-4" />
              Voltar
            </Button>
          </Link>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Informações Básicas</CardTitle>
              <CardDescription>Dados principais do produto</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="space-y-2">
                <Label for="category_id">Categoria</Label>
                <select
                  id="category_id"
                  v-model="form.category_id"
                  class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                >
                  <option value="">Selecione uma categoria (opcional)</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id.toString()">
                    {{ category.name }}
                  </option>
                </select>
                <p v-if="errors.category_id" class="text-sm text-destructive">{{ errors.category_id }}</p>
              </div>

              <div class="space-y-2">
                <Label for="name">Nome do Produto *</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Ex: Vaso Decorativo"
                  required
                />
                <p v-if="errors.name" class="text-sm text-destructive">{{ errors.name }}</p>
              </div>

              <div class="space-y-2">
                <Label for="short_desc">Descrição Curta</Label>
                <Input
                  id="short_desc"
                  v-model="form.short_desc"
                  type="text"
                  placeholder="Breve descrição (máx. 180 caracteres)"
                  maxlength="180"
                />
                <p v-if="errors.short_desc" class="text-sm text-destructive">{{ errors.short_desc }}</p>
              </div>

              <div class="space-y-2">
                <Label for="description">Descrição Completa</Label>
                <Textarea
                  id="description"
                  v-model="form.description"
                  placeholder="Descrição detalhada do produto..."
                  rows="4"
                />
                <p v-if="errors.description" class="text-sm text-destructive">{{ errors.description }}</p>
              </div>

              <div class="space-y-2">
                <Label for="price">Preço *</Label>
                <Input
                  id="price"
                  v-model="form.price"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  required
                />
                <p v-if="errors.price" class="text-sm text-destructive">{{ errors.price }}</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Personalização</CardTitle>
              <CardDescription>Configure se o produto aceita personalização</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="flex items-center space-x-2">
                <Checkbox
                  id="is_personalizable"
                  :checked="form.is_personalizable"
                  @update:checked="(checked: boolean) => form.is_personalizable = checked"
                />
                <Label for="is_personalizable">Este produto pode ser personalizado</Label>
              </div>

              <div v-if="form.is_personalizable" class="space-y-2">
                <Label for="personalization_hint">Dica de Personalização</Label>
                <Input
                  id="personalization_hint"
                  v-model="form.personalization_hint"
                  type="text"
                  placeholder="Ex: Informe o nome para gravação"
                  maxlength="140"
                />
                <p v-if="errors.personalization_hint" class="text-sm text-destructive">{{ errors.personalization_hint }}</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Configurações</CardTitle>
              <CardDescription>Disponibilidade e posicionamento</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="flex items-center space-x-2">
                <Checkbox
                  id="is_available"
                  :checked="form.is_available"
                  @update:checked="(checked: boolean) => form.is_available = checked"
                />
                <Label for="is_available">Produto disponível</Label>
              </div>

              <div class="flex items-center space-x-2">
                <Checkbox
                  id="is_featured"
                  :checked="form.is_featured"
                  @update:checked="(checked: boolean) => form.is_featured = checked"
                />
                <Label for="is_featured">Produto em destaque</Label>
              </div>

              <div class="space-y-2">
                <Label for="position">Posição</Label>
                <Input
                  id="position"
                  v-model.number="form.position"
                  type="number"
                  min="0"
                  placeholder="0"
                />
                <p class="text-sm text-muted-foreground">
                  Define a ordem de exibição (menor número aparece primeiro)
                </p>
                <p v-if="errors.position" class="text-sm text-destructive">{{ errors.position }}</p>
              </div>
            </CardContent>
          </Card>

          <div class="flex gap-4">
            <Button type="submit" class="flex-1">
              Salvar Alterações
            </Button>
            <Link href="/products">
              <Button type="button" variant="outline">
                Cancelar
              </Button>
            </Link>
          </div>
        </form>

        <!-- Photo Gallery Section -->
        <Card class="mt-6">
          <CardHeader>
            <div class="flex items-center justify-between">
              <div>
                <CardTitle>Galeria de Fotos</CardTitle>
                <CardDescription>Adicione até 10 fotos do produto (max 5MB cada)</CardDescription>
              </div>
              <Button @click="selectPhotos" type="button" :disabled="uploadingPhotos">
                <Upload class="mr-2 h-4 w-4" />
                {{ uploadingPhotos ? 'Enviando...' : 'Adicionar Fotos' }}
              </Button>
            </div>
          </CardHeader>
          <CardContent>
            <input
              ref="photoInput"
              type="file"
              accept="image/*"
              multiple
              class="hidden"
              @change="uploadPhotos"
            />

            <div v-if="product.photos && product.photos.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <div
                v-for="photo in product.photos"
                :key="photo.id"
                class="relative group aspect-square rounded-lg overflow-hidden border-2"
                :class="photo.is_cover ? 'border-primary' : 'border-border'"
              >
                <img
                  :src="getPhotoUrl(photo)"
                  :alt="product.name"
                  class="w-full h-full object-cover"
                  loading="lazy"
                />

                <!-- Cover Badge -->
                <div v-if="photo.is_cover" class="absolute top-2 left-2">
                  <Badge variant="default" class="text-xs">
                    <Star class="h-3 w-3 mr-1 fill-current" />
                    Capa
                  </Badge>
                </div>

                <!-- Actions Overlay -->
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                  <Button
                    v-if="!photo.is_cover"
                    @click="setCover(photo.id)"
                    type="button"
                    size="sm"
                    variant="secondary"
                  >
                    <Star class="h-4 w-4" />
                  </Button>
                  <Button
                    @click="deletePhoto(photo.id)"
                    type="button"
                    size="sm"
                    variant="destructive"
                  >
                    <X class="h-4 w-4" />
                  </Button>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-12 text-muted-foreground">
              <Upload class="mx-auto h-12 w-12 mb-4" />
              <p>Nenhuma foto adicionada ainda</p>
              <p class="text-sm mt-1">Clique em "Adicionar Fotos" para começar</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
