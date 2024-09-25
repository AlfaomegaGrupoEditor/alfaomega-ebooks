<script setup lang="ts">
import {reactive, defineEmits, onMounted, computed, watch} from 'vue';
  import Tree from 'vue3-treeview';
  import "vue3-treeview/dist/style.css";
  import { useI18n } from "vue-i18n";
  import { useLibraryStore } from '@/stores'


  const emit = defineEmits(['selected']);

  const { t } = useI18n();
  const libraryStore = useLibraryStore();

  const catalog = computed(() => libraryStore.getCatalog);
  const nodes = reactive({
    all_ebooks: {
      text: t('all_ebooks'),
      state: {
        opened: false,
        checked: true,
      },
      children: ["id11", "id12"], // "id11", "id12"
    },
    id11: {
      text: "Programación",
    },
    id12: {
      text: "Ofimática",
    },
    purchased: {
      text: t('purchased'),
    },
    samples: {
      text: t('samples'),
    },
  });

  const config = {
    roots: ["all_ebooks", "purchased", "samples"],
    padding: 15,
    openedIcon: {
      type: "class",
      class: "fas fa-angle-down"
    },
    closedIcon: {
      type: "class",
      class: "fas fa-angle-right",
      height: "30px",
      width: "30px"
    },
  };

  const handleClick = (node) => {
    emit('selected', node);
  };

  onMounted(() => {
    emit('selected', nodes['all_ebooks']);
    libraryStore.dispatchLoadCatalog();
  });

  watch(catalog, (newVal) => {
    nodes['all_ebooks']['children'] = newVal.root;
    Object.keys(newVal.tree).forEach((key) => {
      nodes[key] = {
        text: newVal.tree[key].title,
        children: newVal.tree[key].children,
      };
    });
  });

</script>

<template>
  <div class="mb-2">
    <h4>{{ $t('digital_library')}}</h4>
    <Tree :nodes="nodes" :config="config" @node-focus="handleClick"></Tree>
  </div>
</template>

<style scoped>

</style>
