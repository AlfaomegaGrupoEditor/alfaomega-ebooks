<script setup lang="ts">
    import {ref, computed} from 'vue';
    import {aoButton} from '@/components';
    import {ToastType} from '@/types';
    import {useI18n} from 'vue-i18n';
    import {API} from '@/services';

    const emit = defineEmits<{ apply: (payload: ToastType) => void }>();
    const {t} = useI18n();

    const code = ref('');
    const processing = ref(false);
    const invalidCode = computed(() => !/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/.test(code.value));

    const test = () => { appStore.testLoading(); };

    const handleClick = async () => {
        processing.value = true;
        const response = await API.library.applyCode(code.value);
        if (response.status === 'success') {
            emit('apply', {
                content: response.message,
                variant: 'success',
                title: t('success')
            } as ToastType);
        } else {
            emit('apply', {
                content: response.message != null
                    ? response.message
                    : t('something_went_wrong'),
                variant: 'primary',
                title: t('failed')
            } as ToastType);
            code.value = '';
        }
        processing.value = false;
    };

</script>

<template>
    <b-card>
        <b-card-title>
            <span class="fs-8">
                {{ $t('add_ebooks') }}
            </span>
        </b-card-title>

        <b-card-text class="fs-8">
            {{ $t('add_sample_text') }}
            {{ $t('purchase_note') }}
        </b-card-text>

        <div class="mb-4">
            <BFormInput
                class="form-control-sm"
                v-model="code"
                :placeholder="t('paste_code_here')"
                type="text"
            />
        </div>

        <div class="row">
            <div class="col">
                <ao-button
                    class="float-end"
                    icon="fa-key"
                    :caption="$t('apply_btn')"
                    :disabled="invalidCode"
                    size="sm"
                    @click="handleClick"
                    :loading="processing"
                />
            </div>
        </div>

        <div class="card-body fs-8 text-muted d-none">
            <span class="fw-bold">{{ $t('note') }}: </span>
            <span>{{ $t('purchase_note') }}</span>
        </div>
    </b-card>
</template>

<style scoped>

</style>
