@extends("components.layout")

@section("title", "Category")

@section("content")
    @if(count($categories) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">View Products</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
            @foreach($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">{{$category->name}}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img
                        src="/storage/uploads/{{$category->image->filename}}"
                        width="100px"
                        height="100px"
                        alt="{{$category->name}}"
                        />
                    </td>
                    <td>
                        <a
                            class="text-blue-500 underline"
                            href="
                            {{route('home', ['category' => $category->id])}}
                        ">
                            show product
                        </a>
                    </td>
                </tr>
            @endforeach
                </tbody>
            </table>
            {{$categories->links()}}
        </div>

    @else
        <h3 class="p-5 text-center">No Category Found</h3>
    @endif
@endSection
