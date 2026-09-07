<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
// import { register } from '@/routes'; // Registro público deshabilitado para entorno institucional
import { store } from '@/routes/login';
import { request } from '@/routes/password';
// import PasskeyVerify from '@/components/PasskeyVerify.vue'; // Acceso passkey deshabilitado

defineOptions({
    layout: {
        title: '',
        description: 'Ingresa los datos para ingresar',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Iniciar sesión - GAD-YAR" />

    <!-- Mensaje de estado (ej. notificación tras restablecer contraseña) -->
    <div
        v-if="status"
        class="rounded-md bg-emerald-50 border border-emerald-200 p-2.5 text-center text-xs font-semibold text-emerald-700"
    >
        {{ status }}
    </div>

    <!-- Passkey deshabilitado -->
    <!-- <PasskeyVerify /> -->

    <!-- 
        GESTIÓN DE TOKEN (CSRF / SESIÓN):
        En Inertia v2 no se requiere un campo oculto '<input type="hidden" name="_token">'.
        Laravel emite la cookie cifrada 'XSRF-TOKEN', la cual Inertia lee y adjunta 
        automáticamente en la cabecera 'X-XSRF-TOKEN' de la petición POST.
        La instrucción 'v-bind="store.form()"' garantiza la llamada segura y tipada.
    -->
    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5 select-text"
    >
        <div class="grid gap-5">
            <!-- Correo Institucional -->
            <div class="grid gap-1.5">
                <Label for="email" class="text-xs font-bold text-slate-700">
                    Correo electrónico institucional
                </Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="usuario@tfja.gob.mx"
                    class="h-10 text-xs bg-white border-slate-300 text-slate-800 placeholder:text-slate-400 focus-visible:border-tfja-blue focus-visible:ring-tfja-blue/20"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Contraseña y Enlace de Recuperación -->
            <div class="grid gap-1.5">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-xs font-bold text-slate-700">
                        Contraseña
                    </Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs font-semibold text-tfja-bronze hover:text-tfja-blue transition-colors"
                        :tabindex="5"
                    >
                        ¿Olvidaste tu contraseña?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="h-10 text-xs bg-white border-slate-300 text-slate-800 placeholder:text-slate-400 focus-visible:border-tfja-blue focus-visible:ring-tfja-blue/20"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Recordar Sesión -->
            <div class="flex items-center justify-between pt-0.5">
                <Label for="remember" class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer font-medium select-none">
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                        class="border-slate-400 data-[state=checked]:bg-tfja-blue data-[state=checked]:border-tfja-blue"
                    />
                    <span>Recordar sesión</span>
                </Label>
            </div>

            <!-- Botón de Envío -->
            <Button
                type="submit"
                class="mt-2 w-full h-10 bg-tfja-blue hover:bg-tfja-blue-hover text-white font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer shadow-sm"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" class="mr-2" />
                Iniciar sesión
            </Button>
        </div>

        <!-- 
            REGISTRO PÚBLICO (DESHABILITADO):
        -->
        <!--
        <div class="text-center text-xs text-slate-600 pt-2 border-t border-slate-300/60">
            ¿No tienes una cuenta?
            <TextLink :href="register()" :tabindex="5" class="text-tfja-bronze hover:text-tfja-blue font-semibold transition-colors ml-1">
                Regístrate
            </TextLink>
        </div>
        -->
    </Form>
</template>