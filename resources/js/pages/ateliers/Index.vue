<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Store, Edit, Eye, MapPin, Phone, Mail, Instagram } from 'lucide-vue-next';

interface Atelier {
    id: number;
    slug: string;
    name: string;
    tagline: string | null;
    bio: string | null;
    phone_whatsapp: string | null;
    instagram_handle: string | null;
    email: string | null;
    city: string | null;
    state: string | null;
    is_active: boolean;
    created_at: string;
}

const props = defineProps<{
    ateliers: Atelier[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Meu Ateliê',
        href: '/ateliers',
    },
];

// Pega o primeiro ateliê (usuário pode ter apenas um)
const atelier = props.ateliers[0];
</script>

<template>
    <Head title="Meu Ateliê" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Meu Ateliê</h2>
                        <p class="text-muted-foreground">
                            Gerencie as informações do seu ateliê
                        </p>
                    </div>
                    <Link :href="`/ateliers/${atelier?.slug}/edit`" v-if="atelier">
                        <Button>
                            <Edit class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                </div>

                <!-- Atelier Details -->
                <div v-if="atelier" class="grid gap-6 md:grid-cols-2">
                    <!-- Informações Básicas -->
                    <Card class="md:col-span-2">
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <CardTitle class="text-2xl">{{ atelier.name }}</CardTitle>
                                        <Badge :variant="atelier.is_active ? 'default' : 'secondary'">
                                            {{ atelier.is_active ? 'Ativo' : 'Inativo' }}
                                        </Badge>
                                    </div>
                                    <CardDescription v-if="atelier.tagline">
                                        {{ atelier.tagline }}
                                    </CardDescription>
                                </div>
                                <Store class="h-8 w-8 text-muted-foreground" />
                            </div>
                        </CardHeader>
                        <CardContent v-if="atelier.bio">
                            <div>
                                <h3 class="font-semibold mb-2">Sobre</h3>
                                <p class="text-sm text-muted-foreground whitespace-pre-wrap">{{ atelier.bio }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Contato -->
                    <Card v-if="atelier.phone_whatsapp || atelier.instagram_handle || atelier.email">
                        <CardHeader>
                            <CardTitle>Informações de Contato</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div v-if="atelier.phone_whatsapp" class="flex items-center gap-3">
                                <Phone class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm">{{ atelier.phone_whatsapp }}</span>
                            </div>
                            <div v-if="atelier.instagram_handle" class="flex items-center gap-3">
                                <Instagram class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm">@{{ atelier.instagram_handle }}</span>
                            </div>
                            <div v-if="atelier.email" class="flex items-center gap-3">
                                <Mail class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm">{{ atelier.email }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Localização -->
                    <Card v-if="atelier.city || atelier.state">
                        <CardHeader>
                            <CardTitle>Localização</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-center gap-3">
                                <MapPin class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm">
                                    {{ atelier.city }}{{ atelier.state ? `, ${atelier.state}` : '' }}
                                </span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- URL Pública -->
                    <Card class="md:col-span-2">
                        <CardHeader>
                            <CardTitle>Link Público</CardTitle>
                            <CardDescription>
                                Compartilhe este link para divulgar seu ateliê
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 rounded bg-muted px-3 py-2 text-sm">
                                    /{{ atelier.slug }}
                                </code>
                                <Button variant="outline" size="sm" as-child>
                                    <a :href="`/${atelier.slug}`" target="_blank">
                                        <Eye class="mr-2 h-4 w-4" />
                                        Visualizar
                                    </a>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- No Atelier -->
                <Card v-else>
                    <CardContent class="flex flex-col items-center justify-center py-16">
                        <Store class="mb-6 h-16 w-16 text-muted-foreground" />
                        <h3 class="mb-3 text-xl font-semibold">Nenhum ateliê encontrado</h3>
                        <p class="mb-6 max-w-md text-center text-sm leading-relaxed text-muted-foreground">
                            Você ainda não criou seu ateliê. Crie agora para começar a divulgar seus produtos!
                        </p>
                        <Link href="/ateliers/create">
                            <Button size="lg">
                                <Store class="mr-2 h-4 w-4" />
                                Criar Ateliê
                            </Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
