<?php

declare(strict_types=1);

namespace ChemLearn\Services;

final class TutorBot
{
    /**
     * Generate a chemistry-focused response based on the latest user prompt.
     *
     * @param string $prompt  The question typed by the learner.
     * @param array<int, array{role:string,message:string}> $history Recent chat history for lightweight context.
     */
    public function respond(string $prompt, array $history = []): string
    {
        $question = trim($prompt);
        if ($question === '') {
            return 'Mình cần câu hỏi cụ thể để có thể hỗ trợ bạn tốt hơn nhé!';
        }

        $lower = mb_strtolower($question);
        $followUp = $this->detectFollowUp($history);

        $knowledgeBase = [
            'oxi' => 'Oxi (O) là phi kim nhóm VIA, trạng thái cơ bản có cấu hình electron [He]2s²2p⁴ và tham gia mạnh vào phản ứng oxi hóa khử.',
            'axit' => 'Axit là chất khi tan trong nước phân li ra ion H⁺. Ví dụ quen thuộc: HCl, H₂SO₄, HNO₃.',
            'bazo' => 'Bazơ là chất phân li ra ion OH⁻ trong dung dịch. NaOH và KOH là hai bazơ mạnh điển hình.',
            'muoi' => 'Muối là sản phẩm của phản ứng trung hòa giữa axit và bazơ. Ví dụ: NaCl, CaCO₃.',
            'este' => 'Este được tạo thành khi axit cacboxylic phản ứng với ancol. Benzyl acetate có mùi hoa nhài rất đặc trưng.',
            'amin' => 'Amin được xem như dẫn xuất của amoniac khi một hay nhiều nhóm H bị thay bởi gốc hiđrocacbon. Ví dụ: metylamin (CH₃NH₂).',
            'polyme' => 'Polyme là các phân tử có khối lượng lớn gồm nhiều mắt xích lặp lại. Cao su buna-S hình thành từ buta-1,3-đien và stiren.',
            'pin' => 'Sức điện động chuẩn của pin Galvani được tính bằng E°catot - E°anot, chọn cặp có hiệu lớn nhất để pin hoạt động mạnh.',
            'nguyên tố' => 'Bạn có thể mở Bảng tuần hoàn trên ChemLearn để xem nhanh thông tin từng nguyên tố!',
            'periodic' => 'Bảng tuần hoàn sắp xếp nguyên tố theo số hiệu nguyên tử tăng dần và tính chất lặp lại theo chu kỳ.',
            'cân bằng' => 'Để cân bằng phương trình, hãy so sánh số nguyên tử ở hai vế và áp dụng phương pháp bảo toàn electron hoặc đại số.',
            'trac nghiem' => 'Mẹo làm trắc nghiệm Hóa: gạch chân dữ kiện quan trọng, ước lượng nhanh, sau đó thay số để kiểm chứng.',
            'rank' => 'Điểm rank tăng khi bạn hoàn thành bài học hoặc làm xong đề thi – cố gắng luyện tập thường xuyên nhé!',
        ];

        foreach ($knowledgeBase as $keyword => $answer) {
            if (str_contains($lower, $keyword)) {
                return $this->formatAnswer($answer, $followUp);
            }
        }

        if (preg_match('/^(h2|o2|h2o|co2|nh3|naoh|hcl)/', $lower)) {
            return 'Bạn đang hỏi về công thức hóa học? Hãy nêu rõ phản ứng hoặc tính chất để mình giải thích chi tiết nhé!';
        }

        if (str_contains($lower, 'xin chào') || str_contains($lower, 'chào')) {
            return 'ChemTutor xin chào! Bạn muốn ôn phần vô cơ, hữu cơ hay bài tập trắc nghiệm nào?';
        }

        if (preg_match('/(magie|mg\^?2\+?)/u', $lower)) {
            return 'Mg²⁺ có cấu hình electron [Ne] và số proton bằng 12. Ion này quan trọng trong diệp lục và chuyển hóa năng lượng ATP.';
        }

        if (preg_match('/(amino acid|aminoaxit|leucine|arginine|aspartic)/u', $lower)) {
            return 'Amino acid tồn tại dạng lưỡng cực tại pH = pI. Ở pH thấp hơn pI chúng nhận thêm H⁺ (dạng cation), còn khi pH lớn hơn pI sẽ ở dạng anion.';
        }

        return $this->formatAnswer(
            'Mình chưa có kiến thức chính xác cho câu hỏi này, nhưng bạn có thể cung cấp thêm dữ kiện (thành phần, số liệu, mục tiêu) để mình gợi ý hướng giải nhé!',
            $followUp
        );
    }

    /**
     * Decide whether to append a follow-up suggestion to keep the conversation flowing.
     *
     * @param array<int, array{role:string,message:string}> $history
     */
    private function detectFollowUp(array $history): ?string
    {
        $lastAssistant = null;
        foreach (array_reverse($history) as $turn) {
            if (($turn['role'] ?? '') === 'assistant') {
                $lastAssistant = $turn['message'] ?? null;
                break;
            }
        }

        if ($lastAssistant === null) {
            return 'Bạn cần mình hỗ trợ bài tập hay giải thích khái niệm nào nữa không?';
        }

        return null;
    }

    private function formatAnswer(string $base, ?string $followUp): string
    {
        if ($followUp === null) {
            return $base;
        }

        return $base . "\n\n" . $followUp;
    }
}
