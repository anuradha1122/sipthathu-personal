<x-app-layout>
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :list="$option" />
            <div class="isolate bg-white px-6 py-10 sm:py-10 lg:px-8">
                <x-profile-heading heading="{{ $user->nameWithInitials }}" subHeading="{{ $user->nic }}" image="{{ $user->profilePicture }}" />

                <x-form-success message="{{ session('success') }}" />
                <form method="POST" action="{{ route('profile.myprofilestore') }}" class="mx-auto mt-8 max-w-xl sm:mt-8" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-6 gap-x-8 gap-y-6 sm:grid-cols-6">
                        @if($category == 'login')
                            <x-form-text-input-section size="sm:col-span-6" name="currentPassword" id="currentPassword" label="Current Password" value="{{ old('currentPassword') }}" />
                            <x-form-text-input-section size="sm:col-span-3" name="newPassword" id="newPassword" label="New Password" value="{{ old('newPassword') }}" />
                            <x-form-text-input-section size="sm:col-span-3" name="confirmPassword" id="confirmPassword" label="Confirm Password" value="{{ old('confirmPassword') }}" />
                        @endif
                        <input type="hidden" name="category" value="{{ $category }}">
                    </div>
                    <div class="mt-10">
                        <x-form-button-primary size="" text="Submit" modelBinding=""/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
