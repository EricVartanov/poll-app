<template>
  <div class="appShell">
    <header class="topNav">
      <nav class="navInner">
        <RouterLink class="brand" to="/create">Polls</RouterLink>
        <RouterLink class="link" to="/create">Create new poll</RouterLink>
      </nav>
    </header>

    <router-view />
  </div>
</template>
