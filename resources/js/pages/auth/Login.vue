
<script setup lang="ts">
import { ref } from 'vue';
import { Form, Head } from '@inertiajs/vue3';
import {
    ArrowRight,
    Eye,
    EyeOff,
    LockKeyhole,
    Mail,
    ShieldCheck,
    Sparkles,
    CheckCircle2,
} from 'lucide-vue-next';

import InputError from '@/components/InputError.vue';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: null,
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const showPassword = ref(false);
</script>

<template>
    <Head title="Connexion | GoRakmana" />

    <div class="gorakmana-login">
        <!-- Background animé -->
        <div class="login-background" aria-hidden="true">
            <div class="background-grid"></div>
            <div class="glow glow-1"></div>
            <div class="glow glow-2"></div>
            <div class="glow glow-3"></div>
            <div class="wave wave-1"></div>
            <div class="wave wave-2"></div>
            <div class="wave wave-3"></div>
        </div>

        <div class="login-content">
            <!-- Branding -->
            <header class="login-brand">
                <div class="brand-name">
                    <span class="brand-red">Go</span><span>Rakmana</span><span class="brand-dot">.</span>
                </div>

                <div class="brand-subtitle">
                    <span class="brand-line"></span>
                    <span>SOLUTIONS DIGITALES SUR MESURE</span>
                    <span class="brand-line"></span>
                </div>
            </header>

            <!-- Card -->
            <section class="login-card">
                <div class="card-top-line"></div>

                <div class="card-heading">
                    <div class="login-icon">
                        <LockKeyhole :size="25" :stroke-width="1.8" />
                    </div>

                    <div class="client-badge">
                        <Sparkles :size="13" />
                        ESPACE CLIENT
                    </div>

                    <h1>Bon retour parmi nous !</h1>

                    <p>
                        Connectez-vous pour accéder à votre
                        espace personnel GoRakmana.
                    </p>
                </div>

                <div v-if="status" class="status-box" role="status">
                    <CheckCircle2 :size="18" />
                    {{ status }}
                </div>

                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="login-form"
                >
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>

                        <div class="form-input">
                            <Mail :size="19" class="field-icon" />

                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="nom@entreprise.com"
                                autocomplete="email"
                                required
                                autofocus
                                tabindex="1"
                            />
                        </div>

                        <InputError :message="errors.email" />
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="field-header">
                            <label for="password">Mot de passe</label>

                            <a
                                v-if="canResetPassword"
                                :href="request()"
                                class="forgot-link"
                                tabindex="5"
                            >
                                Mot de passe oublié ?
                            </a>
                        </div>

                        <div class="form-input">
                            <LockKeyhole :size="19" class="field-icon" />

                            <input
                                id="password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Votre mot de passe"
                                autocomplete="current-password"
                                required
                                tabindex="2"
                            />

                            <button
                                type="button"
                                class="password-toggle"
                                :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                                :aria-pressed="showPassword"
                                @click="showPassword = !showPassword"
                            >
                                <EyeOff v-if="showPassword" :size="19" />
                                <Eye v-else :size="19" />
                            </button>
                        </div>

                        <InputError :message="errors.password" />
                    </div>

                    <!-- Remember -->
                    <label class="remember-label">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            tabindex="3"
                        />
                        <span>Se souvenir de moi</span>
                    </label>

                    <!-- Submit -->
                    <button
                        type="submit"
                        class="submit-button"
                        :disabled="processing"
                        tabindex="4"
                        data-test="login-button"
                    >
                        <span v-if="processing" class="spinner"></span>

                        <template v-else>
                            Se connecter
                            <ArrowRight :size="19" />
                        </template>
                    </button>
                </Form>

                <div class="security-message">
                    <ShieldCheck :size="17" />
                    Connexion sécurisée et confidentielle
                </div>
            </section>

            <footer class="login-footer">
                <p>
                    © {{ new Date().getFullYear() }} GoRakmana.
                    Tous droits réservés.
                </p>
                <p>Solutions digitales sur mesure</p>
            </footer>
        </div>
    </div>
</template>

<style>
/* Reset */
html,
body,
#app {
    min-height: 100%;
    margin: 0;
}

/* FULLSCREEN - Override Laravel AuthLayout */
.gorakmana-login {
    position: fixed !important;
    inset: 0 !important;
    z-index: 9999 !important;

    width: 100vw !important;
    height: 100vh !important;
    height: 100dvh !important;
    max-width: none !important;

    display: flex !important;
    align-items: center;
    justify-content: center;

    overflow-x: hidden;
    overflow-y: auto;

    padding: 35px 20px;

    background: #090b11;
    color: #fff;
    font-family: 'Inter', system-ui, sans-serif;
    isolation: isolate;
}

.gorakmana-login *,
.gorakmana-login *::before,
.gorakmana-login *::after {
    box-sizing: border-box;
}

/* Background */
.login-background {
    position: fixed;
    inset: 0;
    z-index: -1;
    overflow: hidden;
    pointer-events: none;
}

.background-grid {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
    background-size: 55px 55px;
    mask-image: radial-gradient(
        ellipse at center,
        black 10%,
        transparent 85%
    );
}

.glow {
    position: absolute;
    border-radius: 50%;
    filter: blur(90px);
    pointer-events: none;
}

.glow-1 {
    width: 550px;
    height: 550px;
    top: -250px;
    left: -200px;
    background: rgba(225, 29, 54, .25);
    animation: glowFloat1 12s ease-in-out infinite alternate;
}

.glow-2 {
    width: 600px;
    height: 600px;
    bottom: -300px;
    right: -250px;
    background: rgba(225, 29, 54, .22);
    animation: glowFloat2 15s ease-in-out infinite alternate;
}

.glow-3 {
    width: 300px;
    height: 300px;
    top: 40%;
    left: 40%;
    background: rgba(225, 29, 54, .08);
    animation: glowFloat1 10s ease-in-out infinite alternate;
}

/* Waves */
.wave {
    position: absolute;
    width: 160%;
    height: 550px;
    left: -30%;
    bottom: -400px;
    border-radius: 45%;
    border: 1px solid rgba(239, 35, 60, .20);
}

.wave-1 {
    background: linear-gradient(
        180deg,
        rgba(225, 29, 54, .12),
        transparent
    );
    animation: waveRotate 25s linear infinite;
}

.wave-2 {
    bottom: -430px;
    border-color: rgba(239, 35, 60, .13);
    animation: waveRotateReverse 30s linear infinite;
}

.wave-3 {
    bottom: -460px;
    border-color: rgba(239, 35, 60, .08);
    animation: waveRotate 38s linear infinite;
}

/* Content */
.login-content {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 460px;
    margin: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    animation: fadeUp .7s ease both;
}

/* Logo */
.login-brand {
    margin-bottom: 30px;
    text-align: center;
}

.brand-name {
    display: flex;
    align-items: baseline;
    justify-content: center;
    font-size: 43px;
    font-weight: 850;
    letter-spacing: -2.5px;
    line-height: 1.2;
    color: #fff;
}

.brand-red,
.brand-dot {
    color: #ff304a;
}

.brand-subtitle {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 10px;
    color: #a1a1aa;
    font-size: 9px;
    font-weight: 650;
    letter-spacing: 2px;
    white-space: nowrap;
}

.brand-line {
    width: 22px;
    height: 1px;
    background: #e11d36;
}

/* Card */
.login-card {
    position: relative;
    width: 100%;
    padding: 38px 38px 30px;
    background: #fff;
    color: #18181b;
    border-radius: 20px;
    box-shadow:
        0 25px 80px rgba(0, 0, 0, .35),
        0 0 50px rgba(225, 29, 54, .07);
    overflow: hidden;
}

.card-top-line {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 120px;
    height: 3px;
    background: linear-gradient(
        90deg,
        transparent,
        #e11d36,
        transparent
    );
}

.card-heading {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.login-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    margin-bottom: 20px;
    border-radius: 16px;
    background: #fff1f2;
    border: 1px solid #ffe4e6;
    color: #e11d36;
}

.client-badge {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 13px;
    color: #e11d36;
    font-size: 10px;
    font-weight: 750;
    letter-spacing: 1.7px;
}

.card-heading h1 {
    margin: 0;
    color: #18181b;
    font-size: 25px;
    font-weight: 800;
    letter-spacing: -.9px;
    line-height: 1.3;
}

.card-heading p {
    max-width: 310px;
    margin: 12px 0 0;
    color: #71717a;
    font-size: 13px;
    line-height: 1.7;
}

/* Form */
.login-form {
    width: 100%;
    margin-top: 29px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 9px;
    color: #27272a;
    font-size: 12px;
    font-weight: 650;
}

.field-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.forgot-link {
    margin-bottom: 9px;
    color: #e11d36;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
}

.forgot-link:hover {
    text-decoration: underline;
}

.form-input {
    display: flex;
    align-items: center;
    width: 100%;
    height: 50px;
    background: #fafafa;
    border: 1px solid #e4e4e7;
    border-radius: 10px;
    transition: all .2s ease;
}

.form-input:focus-within {
    border-color: #e11d36;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(225, 29, 54, .08);
}

.field-icon {
    flex-shrink: 0;
    margin-left: 15px;
    color: #a1a1aa;
}

.form-input:focus-within .field-icon {
    color: #e11d36;
}

.form-input input {
    flex: 1;
    min-width: 0;
    width: 100%;
    height: 100%;
    padding: 0 12px;
    border: 0;
    outline: 0;
    background: transparent;
    color: #18181b;
    font-size: 13px;
}

.form-input input::placeholder {
    color: #a1a1aa;
}

.password-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 14px;
    border: none;
    background: transparent;
    color: #a1a1aa;
    cursor: pointer;
}

.password-toggle:hover {
    color: #e11d36;
}

/* Remember */
.remember-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 3px 0 25px;
    color: #52525b;
    font-size: 12px;
    cursor: pointer;
}

.remember-label input {
    width: 16px;
    height: 16px;
    accent-color: #e11d36;
}

/* Button */
.submit-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 11px;
    width: 100%;
    height: 51px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(110deg, #ef233c, #be1230);
    color: #fff;
    font-size: 13px;
    font-weight: 750;
    cursor: pointer;
    box-shadow: 0 8px 22px rgba(225, 29, 54, .20);
    transition: all .25s ease;
}

.submit-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(225, 29, 54, .32);
}

.submit-button:disabled {
    opacity: .65;
    cursor: not-allowed;
}

.spinner {
    width: 19px;
    height: 19px;
    border: 2px solid #ffffff55;
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin .7s linear infinite;
}

/* Security */
.security-message {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 25px;
    padding-top: 22px;
    border-top: 1px solid #f4f4f5;
    color: #a1a1aa;
    font-size: 11px;
}

.security-message svg {
    color: #16a34a;
}

/* Status */
.status-box {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
    padding: 12px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 9px;
    color: #15803d;
    font-size: 12px;
}

/* Footer */
.login-footer {
    margin-top: 28px;
    text-align: center;
    color: #a1a1aa;
    font-size: 11px;
    line-height: 1.9;
}

.login-footer p {
    margin: 0;
}

/* Animations */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes glowFloat1 {
    from { transform: translate(0, 0); }
    to { transform: translate(80px, 60px); }
}

@keyframes glowFloat2 {
    from { transform: translate(0, 0); }
    to { transform: translate(-90px, -60px); }
}

@keyframes waveRotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes waveRotateReverse {
    from { transform: rotate(360deg); }
    to { transform: rotate(0deg); }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 600px) {
    .gorakmana-login {
        padding: 25px 16px;
    }

    .login-content {
        max-width: 420px;
    }

    .brand-name {
        font-size: 37px;
    }

    .brand-subtitle {
        font-size: 8px;
        letter-spacing: 1px;
        gap: 7px;
    }

    .brand-line {
        width: 15px;
    }

    .login-card {
        padding: 30px 23px 25px;
        border-radius: 16px;
    }

    .card-heading h1 {
        font-size: 22px;
    }

    .card-heading p {
        font-size: 12px;
    }
}

@media (max-width: 360px) {
    .login-card {
        padding: 26px 17px;
    }

    .brand-subtitle {
        letter-spacing: .5px;
    }

    .forgot-link {
        font-size: 10px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .gorakmana-login *,
    .gorakmana-login *::before,
    .gorakmana-login *::after {
        animation: none !important;
        transition: none !important;
    }
}
</style>
