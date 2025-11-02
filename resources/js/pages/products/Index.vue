<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Edit, Plus, Trash2, Package, Image as ImageIcon } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

interface Product {
  id: number;
  name: string;
  slug: string;
  short_desc: string | null;
  price: string;
  is_available: boolean;
  is_featured: boolean;
  is_personalizable: boolean;
  photos_count: number;
  category: {
    id: number;
    name: string;
  } | null;
  photos: Array<{
    id: number;
    file_path: string;
    thumb_path: string | null;
    medium_path: string | null;
    is_cover: boolean;
  }>;
}

interface Category {
  id: number;
  name: string;
  slug: string;
}

interface Atelier {
  id: number;
  name: string;
  slug: string;
}

const props = defineProps<{
  products: Product[];
  categories: Category[];
  atelier: Atelier;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Produtos',
    href: '/products',
  },
];

const deleteProduct = (slug: string) => {
  if (confirm('Tem certeza que deseja excluir este produto?')) {
    router.delete(`/products/${slug}`);
  }
};

const formatPrice = (price: string) => {
  return `R$ ${parseFloat(price).toFixed(2).replace('.', ',')}`;
};

const getCoverPhoto = (product: Product) => {
  const cover = product.photos.find(p => p.is_cover);
  const photo = cover || product.photos[0];
  // Use thumbnail for listing (smaller and faster)
  return photo?.thumb_path || photo?.file_path;
};
</script>

<template>
  <Head title="Produtos" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h2 class="text-3xl font-bold tracking-tight">Produtos</h2>
            <p class="text-muted-foreground">Gerencie o catálogo de produtos do seu ateliê</p>
          </div>
          <div>
            <Link v-if="categories && categories.length > 0" href="/products/create">
              <Button>
                <Plus class="mr-2 h-4 w-4" />
                Novo Produto
              </Button>
            </Link>
            <Button v-else disabled title="Crie uma categoria primeiro">
              <Plus class="mr-2 h-4 w-4" />
              Novo Produto
            </Button>
          </div>
        </div>

        <div v-if="products.length === 0" class="text-center py-12">
          <Package class="mx-auto h-12 w-12 text-muted-foreground" />
          <h3 class="mt-2 text-sm font-semibold">Nenhum produto</h3>
          <p class="mt-1 text-sm text-muted-foreground">
            {{ !categories || categories.length === 0
              ? 'Antes de adicionar produtos, crie categorias para organizá-los.'
              : 'Comece adicionando seu primeiro produto.'
            }}
          </p>
          <div class="mt-6 flex gap-3 justify-center">
            <Link v-if="!categories || categories.length === 0" href="/categories/create">
              <Button>
                <Plus class="mr-2 h-4 w-4" />
                Criar Categoria
              </Button>
            </Link>
            <Link v-else href="/products/create">
              <Button>
                <Plus class="mr-2 h-4 w-4" />
                Novo Produto
              </Button>
            </Link>
          </div>
        </div>

        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <Card v-for="product in products" :key="product.id" class="overflow-hidden">
            <div v-if="getCoverPhoto(product)" class="aspect-square bg-muted relative">
              <img
                :src="`/storage/${getCoverPhoto(product)}`"
                :alt="product.name"
                class="w-full h-full object-cover"
              />
              <div class="absolute top-2 right-2 flex gap-2">
                <Badge v-if="product.is_featured" variant="default">Destaque</Badge>
                <Badge v-if="product.is_personalizable" variant="secondary">Personalizável</Badge>
              </div>
            </div>
            <div v-else class="aspect-square bg-muted flex items-center justify-center">
              <ImageIcon class="h-12 w-12 text-muted-foreground" />
            </div>

            <CardHeader>
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <CardTitle class="line-clamp-1">{{ product.name }}</CardTitle>
                  <CardDescription v-if="product.short_desc" class="mt-1 line-clamp-2">
                    {{ product.short_desc }}
                  </CardDescription>
                  <div v-if="product.category" class="mt-2">
                    <Badge variant="outline" class="text-xs">{{ product.category.name }}</Badge>
                  </div>
                </div>
              </div>
            </CardHeader>

            <CardContent>
              <div class="flex items-center justify-between mb-4">
                <div>
                  <div class="text-2xl font-bold">{{ formatPrice(product.price) }}</div>
                  <div class="flex items-center gap-2 mt-1">
                    <Badge :variant="product.is_available ? 'default' : 'secondary'" class="text-xs">
                      {{ product.is_available ? 'Disponível' : 'Indisponível' }}
                    </Badge>
                    <span v-if="product.photos_count > 0" class="text-xs text-muted-foreground flex items-center gap-1">
                      <ImageIcon class="h-3 w-3" />
                      {{ product.photos_count }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="flex gap-2">
                <Link :href="`/products/${product.slug}/edit`" class="flex-1">
                  <Button variant="outline" size="sm" class="w-full">
                    <Edit class="mr-1 h-4 w-4" />
                    Editar
                  </Button>
                </Link>
                <Button
                  variant="outline"
                  size="sm"
                  @click="deleteProduct(product.slug)"
                >
                  <Trash2 class="h-4 w-4" />
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
