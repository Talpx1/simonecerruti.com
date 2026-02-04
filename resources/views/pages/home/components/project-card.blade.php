<div
    class="border border-light p-8 2xl:p-4 flex flex-col relative hover:[&>img]:saturate-100 hover:[&>img]:brightness-100 ">
    <img src="https://picsum.photos/1920/1080"
        class="absolute 2xl:static top-0 left-0 p-4 2xl:p-0 -z-1 transition-all duration-300
            2xl:saturate-100 2xl:brightness-100
            saturate-50 brightness-50
            2xl:max-h-1/2 2xl:h-1/2 2xl:min-h-1/2
            max-h-full h-f min-h-full
            object-cover object-center 2xl:mask-b-from-60%">
    <div class="gap-4 flex flex-col justify-between grow">
        <h3 class="text-2xl 2xl:text-4xl font-bold uppercase 2xl:-mt-[1em]">
            Lorem ipsum dolor sit amet.
        </h3>

        <h4 class="text-lg 2xl:text-xl">
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Nesciunt officiis nam odio tempora quo,
            provident aperiam quos quis impedit consequatur!
        </h4>
        <div class="flex justify-between items-center [&>a]:underline [&>a]:underline-offset-4 [&>a]:text-lg">
            <a href="">{{ __('Discover more') }}</a>
            <a href="">{{ __('GitHub') }}</a>
            <a href="">{{ __('Website') }}</a>
        </div>

        <div
            class="flex gap-4 items-center flex-wrap [&>a]:p-1 [&>a]:text-dark [&>a]:bg-light [&>a]:text-sm 2xl:[&>a]:text-lg [&>a]:underline [&>a]:underline-offset-1">
            <a href="">{{ __('#tag1') }}</a>
            <a href="">{{ __('#tag2') }}</a>
            <a href="">{{ __('#tag3') }}</a>
            <a href="">{{ __('#tag4') }}</a>
        </div>
    </div>
</div>
