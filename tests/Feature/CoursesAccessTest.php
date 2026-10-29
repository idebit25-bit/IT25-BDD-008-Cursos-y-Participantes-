<?php

namespace Tests\Feature;

use Tests\TestCase;

class CoursesAccessTest extends TestCase
{
    public function test_guest_is_redirected_to_login_instead_of_rendering_partial_course_layout(): void
    {
        $this->get('/cursos')->assertRedirect(route('login'));
    }
}
