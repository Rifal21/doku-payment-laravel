<nav class="flex items-center justify-between flex-wrap bg-teal-500 p-6">
  <div class="flex items-center flex-shrink-0 text-white mr-6">
    <a href="#" class="text-white no-underline hover:text-white hover:no-underline">
      <span class="text-2xl pl-2"><i class="em em-grinning"></i> Logo</span>
    </a>
  </div>

  <div class="block lg:hidden">
    <button id="menu-toggle" class="flex items-center px-3 py-2 border rounded text-teal-200 border-teal-400 hover:text-white hover:border-white">
      <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <title>Menu</title>
        <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0V15z" />
      </svg>
    </button>
  </div>

  <div id="menu-content" class="w-full hidden lg:flex lg:items-center lg:w-auto">
    <div class="text-sm lg:flex-grow">
      <a href="/" class="block mt-4 lg:inline-block lg:mt-0 text-teal-200 hover:text-white mr-4">
        Home
      </a>
      <a href="/cart" class="block mt-4 lg:inline-block lg:mt-0 text-teal-200 hover:text-white mr-4">
        Keranjang
      </a>
      <a href="#" class="block mt-4 lg:inline-block lg:mt-0 text-teal-200 hover:text-white">
        Contact
      </a>
    </div>

    @if (Auth::check())
    <div>
      <p class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-teal-500 hover:bg-white mt-4 lg:mt-0 lg:ml-3">{{ Auth::user()->name }}</p>
    </div>
    <div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-teal-500 hover:bg-white mt-4 lg:mt-0 lg:ml-3">Logout</button>
      </form>
    </div>
    @else
    <div>
      <a href="/login" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-teal-500 hover:bg-white mt-4 lg:mt-0 lg:ml-3">Sign in</a>
    </div>
    @endif
  </div>
</nav>

<script>
  const menuToggle = document.getElementById('menu-toggle');
  const menuContent = document.getElementById('menu-content');

  menuToggle.addEventListener('click', () => {
    menuContent.classList.toggle('hidden');
  });
</script>
