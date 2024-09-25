<script setup lang="ts">
  import {reactive, defineEmits, onMounted, computed, watch} from 'vue';
  import Tree from 'vue3-treeview';
  import 'vue3-treeview/dist/style.css';
  import {useI18n} from 'vue-i18n';
  import {useLibraryStore} from '@/stores';

  const emit = defineEmits(['selected']);

  const {t} = useI18n();
  const libraryStore = useLibraryStore();
  const catalog = computed(() => libraryStore.getCatalog);

  const nodes = reactive({
    all_ebooks: {
      text: t('all_ebooks'),
      state: {
        opened: false,
        checked: true
      },
      children: []
    },
    purchased: {
      text: t('purchased')
    },
    samples: {
      text: t('samples')
    }
  });
  const config = {
    roots: ['all_ebooks', 'purchased', 'samples'],
    padding: 15,
    openedIcon: {
      type: 'class',
      class: 'fas fa-angle-down'
    },
    closedIcon: {
      type: 'class',
      class: 'fas fa-angle-right',
      height: '30px',
      width: '30px'
    }
  };

  const handleClick = (node) => {
    // TODO: filter by accessType on purchased and samples
    const traverse = (node) => {
      categories += categories === '' ? node.id : `,${node.id}`;
      if (node.children && node.children.length > 0) {
        node.children.forEach((child) => {
          traverse(nodes[child]);
        });
      }
    };

    let categories = null;

    if (node.id !== 'all_ebooks'
        && node.id !== 'purchased'
        && node.id !== 'samples') {
      categories = '';
      traverse(node);
    }

    const urlParams = new URLSearchParams(window.location.search);
    emit('selected', { categories: categories, text: node.text });
  };

  const traverse = (node) => {
    category += category === '' ? node.id : `, ${node.id}`;
    if (node.children) {
      node.children.forEach((child) => {
        traverse(child);
      });
    }
  };

  onMounted(() => {
    emit('selected', nodes['all_ebooks']);
    libraryStore.dispatchLoadCatalog();
  });

  watch(catalog, (newVal) => {
    nodes['all_ebooks']['children'] = newVal.root;
    Object.keys(newVal.tree).forEach((key) => {
      nodes[key] = {
        id: key,
        text: newVal.tree[key].title,
        children: newVal.tree[key].children
      };
    });
  });

</script>

<template>
  <div class="mb-2">
    <h4>{{ $t('digital_library') }}</h4>
    <Tree :nodes="nodes" :config="config" @node-focus="handleClick"></Tree>
  </div>
</template>

<style scoped>

</style>
